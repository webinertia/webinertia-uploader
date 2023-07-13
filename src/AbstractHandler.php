<?php

declare(strict_types=1);

namespace Webinertia\Uploader;

use Laminas\EventManager\EventManagerInterface;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use SplFileInfo;
use Webinertia\Db\AbstractModel;

use const DIRECTORY_SEPARATOR;

use function explode;
use function realpath;
use function sprintf;

abstract class AbstractHandler extends AbstractModel implements UploaderHandlerInterface
{
    public const BASE_PUBLIC_PATH = __DIR__ . '/upload';
    public const BASE_LOCAL_PATH  = __DIR__ . '/../../../../public/upload';
    /** @var string $localPath */
    protected $localPath;
    /** @var string $publicPath */
    protected $publicPath;
    /** @var string $target */
    protected $target;
    /** @var string $type */
    protected $type;
    /** @var string $targetFileName */
    protected $targetFileName;
    /** @var string $module */
    protected $module;
    /**
     *
     * @param array $data
     * @param array $config expects a key matching the class name of the extending class
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected array $config,
        ?TableGateway $gateway = null,
        protected ?RenameUpload $fileHandler = null,
        protected array $data = []
    ) {
        parent::__construct($gateway, $data, $config);
    }

    abstract protected function setLocalPath(string $path): void;
    abstract protected function processParams(array $params): void;

    public function getLocalPath(): string
    {
        return $this->localPath;
    }

    public function getPublicPath(): string
    {
        return $this->publicPath;
    }

    // public function setModule(string $module)
    // {
    //     $this->module = $module;
    // }
    /**
     * $dir = '/dir, or /dir/dir
     *
     * @param string $dir */
    public function setTarget(string $dir): void
    {
        $baseInfo = new SplFileInfo(self::BASE_LOCAL_PATH);
        $this->target = realpath($baseInfo->getRealPath());
        if ($dir[0] !== '/') {
            $this->target .= '/' . $dir;
        } else {
            $this->target .= $dir;
        }
        if ($this->target[-1] !== '/') {
            $this->target .= '/';
        }
        $this->fileHandler->setTarget($this->target);
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    // public function setType(string $type): void
    // {
    //     $this->type = $type;
    // }

    // public function getType(): string
    // {
    //     return $this->type;
    // }

    public function setTargetFileName(string $identifier, ?string $type = null, $overRideType = false): void
    {
        if ($overRideType && $type !== null) {
            $this->targetFileName = sprintf(
                $this->targetFileName,
                $type,
                $identifier
            );
        } else {
            $this->targetFileName = sprintf(
                $this->targetFileName,
                $this->target ?? '',
                $identifier
            );
        }
        //$this->fileHandler->setTarget($this->targetFileName);
    }

    public function getTargetFileName(): string
    {
        return $this->targetFileName;
    }
}
