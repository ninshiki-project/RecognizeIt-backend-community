<?php

namespace App\Http\Concern;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Sanity\Client as SanityClient;
use Sanity\Exception\InvalidArgumentException;

trait HasSanity
{
    /**
     * @throws InvalidArgumentException
     */
    public function upload(Request $request)
    {
        $client = new SanityClient([
            'projectId' => config('sanity.project_id'),
            'dataset' => config('sanity.dataset'),
            'useCdn' => false,
            'apiVersion' => config('sanity.api_version'),
        ]);

        $uud = Str::uuid()->toString();

        return $client->uploadAssetFromString('image', $request->file('image'), [
            'filename' => "{$request->user()->id}-{$uud}.{$request->file('image')->getClientOriginalExtension()}",
            'contentType' => $request->file('image')->getClientMimeType(),
        ]);

    }
}
