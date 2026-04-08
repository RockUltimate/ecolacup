<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomepageMessageRequest;
use App\Models\HomepageMessage;
use Illuminate\Http\RedirectResponse;

class HomepageMessageController extends Controller
{
    public function update(UpdateHomepageMessageRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        HomepageMessage::query()->updateOrCreate(
            ['id' => HomepageMessage::SINGLETON_ID],
            [
                ...HomepageMessage::defaults(),
                'title' => $validated['title'],
                'body' => $validated['body'],
                'updated_by' => $request->user()->id,
            ]
        );

        return redirect()->to(route('udalosti.index').'#home-news')->with('status', 'homepage-message-updated');
    }
}
