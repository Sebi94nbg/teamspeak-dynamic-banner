<?php

namespace App\Jobs;

use App\Models\Template;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DrawGridSystemOnTemplate implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Class properties
     *
     * @var \App\Models\Template
     */
    protected Template $template;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 10;

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->template->id;
    }

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load template as GDImage to be able to modify it
        $source_image_filepath = public_path($this->template->file_path_original).'/'.$this->template->filename;
        $source_image_file_extension = strtolower(pathinfo($source_image_filepath, PATHINFO_EXTENSION));
        $gd_image = ($source_image_file_extension == 'png') ? imagecreatefrompng($source_image_filepath) : imagecreatefromjpeg($source_image_filepath);

        if ($gd_image === false) {
            throw new Exception('Failed to load the source template as image object.');
        }

        $line_color = imagecolorallocatealpha($gd_image, 0, 0, 0, 80);
        $spacing = 25;

        // Draw vertical lines
        for ($iw = 0; $iw < $this->template->width / $spacing; $iw++) {
            if (! imageline($gd_image, $iw * $spacing, 0, $iw * $spacing, $this->template->width, $line_color)) {
                throw new Exception("Failed to draw the vertical line at the x-position `$iw*$spacing` for the grid system.");
            }
        }

        // Draw horizontal lines
        for ($ih = 0; $ih < $this->template->height / $spacing; $ih++) {
            if (! imageline($gd_image, 0, $ih * $spacing, $this->template->width, $ih * $spacing, $line_color)) {
                throw new Exception("Failed to draw the horizontal line at the y-position `$ih*$spacing` for the grid system.");
            }
        }

        $target_directory = $this->template->file_path_drawed_grid;
        if (! Storage::disk('public')->exists($target_directory)) {
            if (! Storage::disk('public')->makeDirectory($target_directory, 0755, true, true)) {
                throw new Exception("Failed to create the necessary target directory `$target_directory`.");
            }
        }

        // Save the generated image as file
        $target_directory = public_path($this->template->file_path_drawed_grid);

        if ($source_image_file_extension == 'png') {
            if (! imagepng($gd_image, $target_directory.'/'.$this->template->filename, 0)) {
                throw new Exception("Could not save the template with the drawed grid system to `$target_directory`.");
            }
        } else {
            if (! imagejpeg($gd_image, $target_directory.'/'.$this->template->filename, 100)) {
                throw new Exception("Could not save the template with the drawed grid system to `$target_directory`.");
            }
        }

        CloneOriginalTemplate::dispatch($this->template, $this->template->file_path_drawed_grid_text, true);
        CloneOriginalTemplate::dispatch($this->template, $this->template->file_path_drawed_text);
    }
}
