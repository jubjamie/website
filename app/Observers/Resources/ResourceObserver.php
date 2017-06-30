<?php

namespace App\Observers\Resources;

use App\Resource;
use App\Traits\DeletesDirectory;


class ResourceObserver
{
    use DeletesDirectory;
    
    /**
     * Listen to the Resource deleted event.
     * @param Resource $resource
     * @return bool
     */
    public function deleted(Resource $resource)
    {
        // If a file, delete the directory
        if($resource->isFile()) {
            $this->rmdir($resource->getPath());
        }
    }
}