<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateAddRequest;
use App\Http\Requests\TemplateUpdateRequest;
use App\Jobs\DrawGridSystemOnTemplate;
use App\Models\Template;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TemplateController extends Controller
{
    /**
     * Properties / Settings
     */
    private string $upload_directory_original = 'uploads/templates';

    private string $upload_directory_drawed_grid = 'uploads/templates/drawed_grid';

    private string $upload_directory_drawed_grid_text = 'uploads/templates/drawed_grid_text';

    private string $upload_directory_drawed_text = 'uploads/templates/drawed_text';

    /**
     * Display the main page.
     */
    public function overview(): View
    {
        return view('templates')->with('templates', Template::all());
    }

    /**
     * Display the add view.
     */
    public function add(): View
    {
        return view('template.add');
    }

    /**
     * Save a new data set.
     */
    public function save(TemplateAddRequest $request): RedirectResponse
    {
        $request->validated();

        $filename = time().'_'.$request->file->getClientOriginalName();
        $request->file->move(public_path($this->upload_directory_original), $filename);

        list($width, $height) = getimagesize($this->upload_directory_original.'/'.$filename);

        $template = Template::create([
            'alias' => $request->alias,
            'filename' => $filename,
            'file_path_original' => $this->upload_directory_original,
            'file_path_drawed_grid' => $this->upload_directory_drawed_grid,
            'file_path_drawed_grid_text' => $this->upload_directory_drawed_grid_text,
            'file_path_drawed_text' => $this->upload_directory_drawed_text,
            'width' => $width,
            'height' => $height,
        ]);

        DrawGridSystemOnTemplate::dispatch($template);

        if (! $template->save()) {
            return Redirect::route('template.add')->withInput($request->all())->with([
                'error' => 'template-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        return Redirect::route('template.edit', ['template_id' => $template->id])->with([
            'success' => 'template-add-successful',
            'message' => 'Successfully added the new template.',
        ]);
    }

    /**
     * Display the edit view.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        try {
            $template = Template::findOrFail($request->template_id);
        } catch (ModelNotFoundException) {
            return Redirect::route('templates')
                ->withInput($request->all())
                ->with([
                    'error' => 'template-not-found',
                    'message' => 'The template, which you have tried to edit, does not exist.',
                ]);
        }

        return view('template.edit', ['template_id' => $template->id])->with([
            'template' => $template,
        ]);
    }

    /**
     * Update an existing data set.
     */
    public function update(TemplateUpdateRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            $template = Template::findOrFail($request->template_id);
        } catch (ModelNotFoundException) {
            return Redirect::route('template.edit', ['template_id' => $template->id])
                ->withInput($request->all())
                ->with([
                    'error' => 'template-not-found',
                    'message' => 'The template, which you have tried to edit, does not exist.',
                ]);
        }

        $template->alias = $request->alias;

        if ($request->file) {
            $file_path_original = public_path($template->file_path_original).'/'.$template->filename;
            if (file_exists($file_path_original)) {
                unlink($file_path_original);
            }

            $upload_directory_drawed_grid = public_path($template->upload_directory_drawed_grid).'/'.$template->filename;
            if (file_exists($upload_directory_drawed_grid)) {
                unlink($upload_directory_drawed_grid);
            }

            $file_path_drawed_grid_text = public_path($template->file_path_drawed_grid_text).'/'.$template->filename;
            if (file_exists($file_path_drawed_grid_text)) {
                unlink($file_path_drawed_grid_text);
            }

            $file_path_drawed_text = public_path($template->file_path_drawed_text).'/'.$template->filename;
            if (file_exists($file_path_drawed_text)) {
                unlink($file_path_drawed_text);
            }

            $filename = time().'_'.$request->file->getClientOriginalName();
            $request->file->move(public_path($this->upload_directory_original), $filename);

            list($width, $height) = getimagesize($this->upload_directory_original.'/'.$filename);

            $template->filename = $filename;
            $template->file_path_original = $this->upload_directory_original;
            $template->file_path_drawed_grid = $this->upload_directory_drawed_grid;
            $template->file_path_drawed_grid_text = $this->upload_directory_drawed_grid_text;
            $template->file_path_drawed_text = $this->upload_directory_drawed_text;
            $template->width = $width;
            $template->height = $height;

            DrawGridSystemOnTemplate::dispatch($template);
        }

        if (! $template->save()) {
            return Redirect::route('template.edit', ['template_id' => $template->id])
                ->withInput($request->all())
                ->with([
                    'error' => 'template-edit-error',
                    'message' => 'Failed to update the database entry. Please try again.',
                ]);
        }

        return Redirect::route('template.edit', ['template_id' => $template->id])->with([
            'success' => 'template-edit-successful',
            'message' => 'Successfully updated the template.',
        ]);
    }

    /**
     * Delete the template.
     */
    public function delete(Request $request): RedirectResponse
    {
        try {
            $template = Template::findOrFail($request->template_id);
        } catch (ModelNotFoundException) {
            return Redirect::route('templates')->with([
                'error' => 'template-not-found',
                'message' => 'The template, which you have tried to delete, does not exist.',
            ]);
        }

        $file_path_original = public_path($template->file_path_original).'/'.$template->filename;
        if (file_exists($file_path_original)) {
            unlink($file_path_original);
        }

        $upload_directory_drawed_grid = public_path($template->upload_directory_drawed_grid).'/'.$template->filename;
        if (file_exists($upload_directory_drawed_grid)) {
            unlink($upload_directory_drawed_grid);
        }

        $file_path_drawed_grid_text = public_path($template->file_path_drawed_grid_text).'/'.$template->filename;
        if (file_exists($file_path_drawed_grid_text)) {
            unlink($file_path_drawed_grid_text);
        }

        $file_path_drawed_text = public_path($template->file_path_drawed_text).'/'.$template->filename;
        if (file_exists($file_path_drawed_text)) {
            unlink($file_path_drawed_text);
        }

        if (! $template->delete()) {
            return redirect()->back()->with([
                'error' => 'template-delete-error',
                'message' => 'Failed to delete the template from the database. Please try again.',
            ]);
        }

        return Redirect::route('templates')->with([
            'success' => 'template-delete-successful',
            'message' => 'Successfully deleted the template.',
        ]);
    }
}
