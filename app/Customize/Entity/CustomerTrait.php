<?php
 
namespace Customize\Entity;
 
// use Customize\Entity\Master\CustomerType;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;
 
/**
* @EntityExtension("Eccube\Entity\Customer")
*/
trait CustomerTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line_user_id;

    // /**
    //  * @ORM\Column(type="string", length=255, nullable=true)
    //  */
    // private $yahoo_user_id;

    // /**
    //  * @ORM\Column(type="string", length=255, nullable=true)
    //  */
    // private $firebase_uid;
 
    public function getLineUserId(): ?string
    {
        return $this->line_user_id;
    }
 
    public function setLineUserId(?string $line_user_id): self
    {
        $this->line_user_id = $line_user_id;
 
        return $this;
    }

    // public function getYahooUserId(): ?string
    // {
    //     return $this->yahoo_user_id;
    // }

    // public function setYahooUserId(?string $yahoo_user_id): self
    // {
    //     $this->yahoo_user_id = $yahoo_user_id;

    //     return $this;
    // }

    // public function getFirebaseUserId(): ?string
    // {
    //     return $this->firebase_uid;
    // }
 
    // public function setFirebaseUserId(?string $firebase_uid): self
    // {
    //     $this->firebase_uid = $firebase_uid;
 
    //     return $this;
    // }
}