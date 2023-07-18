<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\Filter\File\RenameUpload;
use SplFileInfo;
use Webinertia\Db\AbstractModel;
use Webinertia\Filter\Uuid;

use function realpath;

abstract class AbstractHandler extends AbstractModel implements UploaderHandlerInterface
{
    public const BASE_PUBLIC_PATH = __DIR__ . '/upload';
    public const BASE_LOCAL_PATH  = __DIR__ . '/../../../../public/upload';

    /** @var array<string, string> $messageTemplate */
    protected $messageTemplate = [
        'FILE_NOT_FOUND'   => 'The requested file could not be found.',
        'FILE_NOT_DELETED' => 'File could not be deleted.'
    ];
    /** @var array $columns */
    protected array $columnMap = [
        'id',
        'uuid',
        'userId',
        'fileName',
        'target',
        'role',
        'privilege',
        'uploadDate'
    ];

    public function __construct(
        protected array $config,
        ?TableGateway $gateway = null,
        protected ?RenameUpload $fileHandler = null,
        protected ?Uuid $uuid = null,
        protected array $data = []
    ) {
        parent::__construct($gateway, $data, $config);
    }

    public function exchangeArray($data)
    {
        if (! isset($data['target'])) {
            throw new \RuntimeException('target must be set');
        }
        if ($this->fileHandler instanceof RenameUpload) {
            $data['target'] = $this->normalizeTarget((string) $data['target']);
        }
        parent::exchangeArray($data);
        if ($this->fileHandler instanceof RenameUpload) {
            if ($this->offsetExists('targetFileName')) {
                $this->fileHandler->setTarget($this->offsetGet('target') . $this->offsetGet('targetFileName'));
            } else {
                $this->fileHandler->setTarget($this->offsetGet('target'));
            }
        }
    }

    /**
     * $dir = '/dir, /dir/dir, /dir/dir/
     *
     * @param string $dir */
    public function normalizeTarget(string $dir): string
    {
        $baseInfo = new SplFileInfo(self::BASE_LOCAL_PATH);
        $target = realpath($baseInfo->getRealPath());
        if ($dir[0] !== '/') {
            $target .= '/' . $dir;
        } else {
            $target .= $dir;
        }
        if ($target[-1] !== '/') {
            $target .= '/';
        }
        return $target;
    }
}
