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

class CloneOriginalTemplate implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The template model to be used here.
     *
     * @var \App\Models\Template
     */
    protected Template $template;

    /**
     * The target directory, where to clone the $template to.
     *
     * @var string
     */
    protected string $target_directory;

    /**
     * Either if the original $template or the one with the grid system should be copied.
     *
     * @var bool
     */
    protected string $use_grid_template;

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
        return $this->template->id.'_'.$this->target_directory;
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
    public function __construct(Template $template, string $target_directory, bool $use_grid_template = false)
    {
        $this->template = $template;
        $this->target_directory = $target_directory;
        $this->use_grid_template = $use_grid_template;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! Storage::disk('public')->exists($this->target_directory)) {
            if (! Storage::disk('public')->makeDirectory($this->target_directory, 0755, true, true)) {
                throw new Exception("Failed to create the necessary target directory `$this->target_directory`.");
            }
        }

        if ($this->use_grid_template) {
            $source_file_path = $this->template->file_path_drawed_grid.'/'.$this->template->filename;
        } else {
            $source_file_path = $this->template->file_path_original.'/'.$this->template->filename;
        }

        if (! Storage::disk('public')->copy($source_file_path, $this->target_directory.'/'.$this->template->filename)) {
            throw new Exception("Failed to copy the file `$source_file_path` to the necessary target directory `$this->target_directory`.");
        }
    }
}
