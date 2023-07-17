<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\Diactoros\UploadedFile;
use Laminas\EventManager\ResponseCollection;
use Laminas\Filter\BaseName;
use Laminas\Filter\Exception\InvalidArgumentException;
use Laminas\Filter\Exception\RuntimeException;
use RecursiveArrayIterator;
use SplFileInfo;
use Webinertia\Db\ModelTrait;
use Webinertia\Filter\Uuid;

use function iterator_apply;
use function unlink;

class UploadManager extends AbstractHandler
{
    use ModelTrait;

    public function handleUpload(UploaderEvent $event)
    {
        $request = $event->getRequest();
        $this->exchangeArray($event->getParams());
        $files    = $request->getUploadedFiles();
        $iterator = new RecursiveArrayIterator($files);
        iterator_apply($iterator, self::class . '::fileSearch', [$iterator]);
        return 'last';
    }

    public function handleDelete(UploaderEvent $event)
    {
        $params = $event->getParams();
        if (! isset($params['uuid'])) {
            throw new Exception\RunTimeException('Delete Event requires the file uuid to be passed as a param');
        }
        $data = $this->fetchByColumn('uuid', $params['uuid']);
        if ($data instanceof self) {
            $fileInfo = new SplFileInfo($data->target . $data->fileName);
            if ($fileInfo->isFile()) {
                if(unlink($fileInfo->getRealPath())) {
                    // this must use offsetGet due to uuid being a class property
                    if ($this->delete(['uuid' => $data->offsetGet('uuid')])) {
                        return $data->offsetGet('uuid');
                    } else {

                    }
                }
            }
        }
    }

    protected function fileSearch($iterator)
    {
        while ($iterator->valid() ) {
            if ($iterator->hasChildren()) {
                $current = $iterator->current();
                if ($current instanceof UploadedFile) {
                    $this->processUploadedFile($current);
                }
                $this->fileSearch($iterator->getChildren());
            } else {
                $current = $iterator->current();
                if ($current instanceof UploadedFile) {
                    $this->processUploadedFile($current);
                }
            }
            $iterator->next();
        }
    }

    protected function processUploadedFile(UploadedFile $file)
    {
        $nameFilter = new BaseName();
        $uuid       = new Uuid();
        $uploaded   = $this->fileHandler->filter($file);
        $metaData   = $uploaded->getStream()->getMetadata();
        if (isset($metaData['uri']) && is_string($metaData['uri'])) {
            $this->offsetSet('fileName', $nameFilter->filter($metaData['uri']));
            $this->offsetSet('uuid', $uuid->filter(null));
            $this->save($this);
        }
    }
}
