<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Diactoros\UploadedFile;
use Laminas\Filter\BaseName;
use RecursiveArrayIterator;
use SplFileInfo;
use Webinertia\Db\Exception\RecordNotFound;
use Webinertia\Db\ModelTrait;
use Webinertia\Filter\Uuid;

use function iterator_apply;
use function unlink;

class UploadManager extends AbstractHandler
{
    use ModelTrait;

    /** @var array<int, array> $uploaded */
    protected array $uploaded = [];

    public function handleUpload(UploaderEvent $event): array
    {
        $request = $event->getRequest();
        $this->exchangeArray($event->getParams());
        $files    = $request->getUploadedFiles();
        $iterator = new RecursiveArrayIterator($files);
        iterator_apply($iterator, self::class . '::fileSearch', [$iterator]);
        return $this->uploaded;
    }

    /**
     * @param UploaderEvent $event
     * @return array
     * @throws Exception\RunTimeException
     * @throws RecordNotFound
     */
    public function handleDelete(UploaderEvent $event): array
    {
        $deleted = [];
        $params = $event->getParams();
        if (! isset($params['uuid'])) {
            throw new Exception\RunTimeException('Delete Event requires the file uuid to be passed as a param');
        }
        /** @var ResultSetInterface|HydratingResultSet $fileData */
        $resultSet = $this->fetchByColumn('uuid', $params['uuid']);
        if ($resultSet->count() === 0) {
            throw new RecordNotFound('The requested records could not be found');
        }

        foreach ($resultSet as $file) {
            if ($file instanceof self) {
                $data = $file->getArrayCopy();
                $result = $this->deleteFile(new SplFileInfo($data['target'] . $data['fileName']));
                if ($result && $this->delete(['uuid' => $data['uuid']]) !== 0) {
                    $deleted[] = [$data['uuid'] => $data['fileName']];
                }
            }
        }
        return $deleted;
    }

    protected function deleteFile(SplFileInfo $file): bool
    {
        if (! $file->isFile()) {
            throw new Exception\RunTimeException('File not found.');
        }
        return unlink($file->getRealPath());
    }
    protected function fileSearch($iterator)
    {
        while ($iterator->valid() ) {
            if ($iterator->hasChildren()) {
                $current = $iterator->current();
                if ($current instanceof UploadedFile) {
                    $this->uploaded[] = $this->processUploadedFile($current);
                }
                $this->fileSearch($iterator->getChildren());
            } else {
                $current = $iterator->current();
                if ($current instanceof UploadedFile) {
                    $this->uploaded[] = $this->processUploadedFile($current);
                }
            }
            $iterator->next();
        }
        return $this->uploaded;
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
        return $this->getArrayCopy();
    }
}
