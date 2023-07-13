<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\Diactoros\UploadedFile;
use Laminas\EventManager\ResponseCollection;
use Laminas\Filter\Exception\InvalidArgumentException;
use Laminas\Filter\Exception\RuntimeException;
use Webinertia\Db\ModelTrait;

use RecursiveArrayIterator;

use function iterator_apply;

final class UploadManager extends AbstractHandler
{
    use ModelTrait;

    protected function processParams(array $params): void { }
    /**
     * todo update
     * webinertia/webinertia-db
     * webinertia/webinertia-uploader
     * webinertia/webinertia-mvc
     * webinertia/webinertia-thememanager (check this one first)
     *
     * todo finish file handling and moving
     */
    public function handleUpload(UploaderEvent $event)
    {
        $request = $event->getRequest();
        $this->setTarget($event->getTarget());
        $files    = $request->getUploadedFiles();
        $iterator = new RecursiveArrayIterator($files);
        iterator_apply($iterator, self::class . '::fileSearch', [$iterator]);
        return 'last';
    }

    protected function fileSearch($iterator)
    {
        while ($iterator->valid() ) {
            if ($iterator->hasChildren() ) {
                $current = $iterator->current();
                if ($current instanceof UploadedFile) {
                    $uploaded = $this->fileHandler->filter($current);
                    $this->gateway->insert(['fileName' => $uploaded->getClientFilename()]);
                }
                $this->fileSearch($iterator->getChildren());
            }
            $iterator->next();
        }
    }

    public function handleDelete(UploaderEvent $event)
    {

    }

    protected function setLocalPath(string $path): void
    {

    }
}
