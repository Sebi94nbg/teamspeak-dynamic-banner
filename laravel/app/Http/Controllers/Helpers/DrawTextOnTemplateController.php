<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Models\BannerTemplate;
use Exception;
use GdImage;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;

class DrawTextOnTemplateController extends Controller
{
    /**
     * Class properties
     */
    private ?GdImage $gd_image = null;

    /**
     * The class destructor
     */
    public function __destruct()
    {
        // Destroy generated image to free up the memory
        if (($this->gd_image !== null) and ($this->gd_image !== false)) {
            imagedestroy($this->gd_image);
        }
    }

    /**
     * Draws the configured text on the template(s) (image(s)) and either writes it to the storage or returns it from the memory without saving it.
     */
    public function draw_text_to_image(BannerTemplate $banner_template, bool $write_to_filesystem, bool $only_original, string $ip_address)
    {
        if ($only_original) {
            $files_to_update = [
                $banner_template->template->file_path_original => $banner_template->template->file_path_drawed_text,
            ];
        } else {
            $files_to_update = [
                $banner_template->template->file_path_original => $banner_template->template->file_path_drawed_text,
                $banner_template->template->file_path_drawed_grid => $banner_template->template->file_path_drawed_grid_text,
            ];
        }

        // Get current variables
        $variables_and_values = [];
        try {
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner_template->banner->instance->id.'_datetime'));
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner_template->banner->instance->id.'_servergrouplist'));
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$banner_template->banner->instance->id.'_virtualserver_info'));
        } catch (ConnectionException $connection_exception) {
            throw new Exception($connection_exception->getMessage());
        }

        $banner_variable_helper = new BannerVariableController(null);
        $variables_and_values = array_merge($variables_and_values, $banner_variable_helper->get_client_specific_info_from_cache($banner_template->banner->instance, $ip_address));

        foreach ($files_to_update as $source_path => $target_path) {
            // Load template as GDImage to be able to modify it
            $source_image_filepath = $source_path.'/'.$banner_template->template->filename;
            $source_image_file_extension = strtolower(pathinfo($source_image_filepath, PATHINFO_EXTENSION));
            $this->gd_image = ($source_image_file_extension == 'png') ? imagecreatefrompng($source_image_filepath) : imagecreatefromjpeg($source_image_filepath);

            if ($this->gd_image === false) {
                throw new Exception('Failed to load the source template as image object.');
            }

            // Write all configured texts to the image
            foreach ($banner_template->configurations as $configuration) {
                // Convert HEX to RGB color code(s) and set it as text font color
                list($red, $green, $blue) = sscanf($configuration->font_color_in_hexadecimal, '#%02x%02x%02x');
                $font_color = imagecolorallocate($this->gd_image, $red, $green, $blue);

                if ($font_color === false) {
                    throw new Exception('Failed to allocate the color for the text.');
                }

                // Search for variables (`%SAMPLE_VARIABLE%`) and replace it with their current value, if possible
                $text = $configuration->text;

                if (preg_match_all("/%[A-Z0-9\_?]+%/", $text, $text_variables)) {
                    foreach ($text_variables[0] as $variable) {
                        $variable_name = strtoupper(str_replace('%', '', $variable));

                        if (array_key_exists($variable_name, $variables_and_values)) {
                            $text = str_replace("$variable", $variables_and_values[$variable_name], $text);
                        } else {
                            $text = str_replace("$variable", 'Unknown', $text);
                        }
                    }
                }

                // Write text to the image
                if (! imagestring($this->gd_image,
                    $configuration->font_size,
                    $configuration->x_coordinate,
                    $configuration->y_coordinate,
                    $text,
                    $font_color
                )) {
                    throw new Exception("Failed to write the text `$text` to the image.");
                }
            }

            // Return the generated image or write it to the storage
            if ($write_to_filesystem) {
                $image_file_path = public_path($target_path.'/'.$banner_template->template->filename);
            } else {
                $image_file_path = null;
            }

            if ($source_image_file_extension == 'png') {
                if ($only_original) {
                    return imagepng($this->gd_image, $image_file_path, 0);
                } else {
                    imagepng($this->gd_image, $image_file_path, 0);
                }
            } else {
                if ($only_original) {
                    return imagejpeg($this->gd_image, $image_file_path, 100);
                } else {
                    imagejpeg($this->gd_image, $image_file_path, 100);
                }
            }
        }
    }
}
