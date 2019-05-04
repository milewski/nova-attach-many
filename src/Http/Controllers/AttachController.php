<?php

namespace NovaAttachMany\Http\Controllers;

use App\Models\Block;
use Laravel\Nova\Resource;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class AttachController extends Controller
{
    public function create(NovaRequest $request, $parent)
    {
        $payload = $this->parsePayload($request);

        return [
            'available' => $this->getAvailableResources($request, $payload->relatedResourceClass),
        ];
    }

    public function edit(NovaRequest $request, $parent, $parentId)
    {
        $payload = $this->parsePayload($request);

        $relationship = $payload->relationship;
        $relationshipClass = $payload->relationshipClass;
        $relatedResourceClass = $payload->relatedResourceClass;
        $resourceId = $payload->resourceId;

        $modelInstance = $relationshipClass::find($resourceId);

        return [
            'selected' => $modelInstance ? $modelInstance->{$relationship}->pluck('id') : [],
            'available' => $this->getAvailableResources($request, $relatedResourceClass),
        ];
    }

    public function getAvailableResources($request, $relationshipClass)
    {
        $query = $relationshipClass::newModel();

        return $relationshipClass::relatableQuery($request, $query)->get()
            ->mapInto($relationshipClass)
            ->filter(function ($resource) use ($request) {
                return $request->newResource()->authorizedToAttach($request, $resource->resource);
            })->map(function($resource) {
                return [
                    'display' => $resource->title(),
                    'value' => $resource->getKey(),
                ];
            })->sortBy('display')->values();
    }

    private function parsePayload($request) {
        return json_decode(base64_decode($request->query('payload')));
    }
}
