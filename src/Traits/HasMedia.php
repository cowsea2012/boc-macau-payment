<?php

namespace Byross\LighthouseMediaLibrary\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMedia
{

    /**
     * Get the "media" relationship.
     *
     * @return MorphToMany
     *
     * @traitUses Illuminate\Database\Eloquent\Model;
     */
    public function media() : MorphToMany
    {
        return $this
            ->morphToMany(config('media.model'), 'mediable')
            ->withPivot('group');
    }
}
