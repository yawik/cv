<?php

declare(strict_types=1);

namespace Cv\Service;


use Applications\Entity\Application;
use Auth\Entity\UserInterface;
use Core\Entity\FileMetadata;
use Core\Entity\ImageMetadata;
use Core\Entity\PermissionsInterface;
use Core\Service\FileManager;
use Cv\Entity\Attachment as CvAttachment;
use Cv\Entity\ContactImage;
use Cv\Entity\Cv;
use Cv\Entity\Cv as CvEntity;
use Cv\Repository\Cv as CvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;
use Psr\Container\ContainerInterface;

class UploadHandler
{
    /**
     * @var CvRepository|ObjectRepository
     */
    private ObjectRepository $cvRepo;
    /**
     * @var DocumentManager
     */
    private DocumentManager $dm;
    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    public function __construct(
        DocumentManager $dm,
        FileManager $fileManager
    )
    {
        $this->cvRepo = $dm->getRepository(CvEntity::class);
        $this->dm = $dm;
        $this->fileManager = $fileManager;
    }

    public static function factory(ContainerInterface $container): self
    {
        $dm = $container->get(DocumentManager::class);
        $fileManager = $container->get(FileManager::class);

        return new self($dm, $fileManager);
    }

    /**
     * @param Application $application
     * @param UserInterface $user
     * @return CvEntity
     * @since 0.26
     */
    public function createFromApplication(Application $application, UserInterface $user)
    {
        $repository = $this->cvRepo;
        $cv = $repository->create();
        $cv->setContact($application->getContact());

        $assignedUser = $application->getJob()->getUser();
        $cv->setUser($assignedUser);

        $perms = $cv->getPermissions();

        $perms->inherit($application->getPermissions());
        // grant view permission to the user that issued this creation.
        $perms->grant($user, PermissionsInterface::PERMISSION_VIEW);
        // revoke change permission to the original applicant
        $perms->revoke($application->getUser(), PermissionsInterface::PERMISSION_CHANGE);

        $applicationAttachments = $application->getAttachments();

        if (count($applicationAttachments) > 0) {
            $cvAttachments = [];
            $fileManager = $this->fileManager;

            foreach ($applicationAttachments as $from) {
                //$gridfs = new \MongoGridFS($this->dm->getClient());
                //$file = new \MongoGridFSFile($gridfs, $appAttachment->getFile());

                /*
                $cvAttachment = new CvAttachment();
                $cvAttachment->setName($applicationAttachment->getName());
                $cvAttachment->setType($applicationAttachment->getType());
                $cvAttachment->setUser($assignedUser);
                $cvAttachment->setFile($file);
                $cvAttachment->setDateUploaded($applicationAttachment->getDateUploaded());
                */
                $fromMetadata = $from->getMetadata();
                $metadata = new FileMetadata();
                $metadata->setContentType($fromMetadata->getContentType());
                $metadata->setUser($assignedUser);

                $fromStream = $fileManager->getStream($from);
                $toAttachment = $fileManager->uploadFromStream(
                    CvAttachment::class,
                    $metadata,
                    $from->getName(),
                    $fromStream
                );
                $cvAttachments[] = $toAttachment;
            }

            $cv->setAttachments(new ArrayCollection($cvAttachments));
        }

        return $cv;
    }

    public function handleUpload(
        Cv $cv,
        array $fileInfo
    )
    {
        $dm = $this->dm;
        $fileManager = $this->fileManager;
        $metadata = new ImageMetadata();

        $contact = $cv->getContact();
        if(!is_null($contact->getImage())){
            $contact->setImage(null);
            $dm->persist($cv);
            $dm->flush();
        }

        $metadata
            ->setUser($cv->getUser())
            ->setContentType($fileInfo['type'])
            ->setKey('original');

        $dm->persist($cv->getUser());

        $file = $fileManager->uploadFromFile(
            ContactImage::class,
            $metadata,
            $fileInfo['tmp_name'],
            $fileInfo['name']
        );

        $cv->getContact()->setImage($file);
        $dm->persist($cv);
        $dm->flush();
    }
}