<?php

namespace ImmortalchessNetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Postparsed
 *
 * @ORM\Table(name="postparsed", indexes={@ORM\Index(name="dateline", columns={"dateline"})})
 * @ORM\Entity
 */
class Postparsed
{
    /**
     * @var integer
     *
     * @ORM\Column(name="postid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $postid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="styleid", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $styleid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="languageid", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $languageid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="dateline", type="integer", nullable=false)
     */
    private $dateline = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="hasimages", type="smallint", nullable=false)
     */
    private $hasimages = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="pagetext_html", type="text", nullable=true)
     */
    private $pagetextHtml;

    /**
     * @return int
     */
    public function getPostid(): int
    {
        return $this->postid;
    }

    /**
     * @param int $postid
     * @return Postparsed
     */
    public function setPostid(int $postid): self
    {
        $this->postid = $postid;

        return $this;
    }

    /**
     * @return int
     */
    public function getStyleid(): int
    {
        return $this->styleid;
    }

    /**
     * @param int $styleid
     * @return Postparsed
     */
    public function setStyleid(int $styleid): self
    {
        $this->styleid = $styleid;

        return $this;
    }

    /**
     * @return int
     */
    public function getLanguageid(): int
    {
        return $this->languageid;
    }

    /**
     * @param int $languageid
     * @return Postparsed
     */
    public function setLanguageid(int $languageid): self
    {
        $this->languageid = $languageid;

        return $this;
    }

    /**
     * @return int
     */
    public function getDateline(): int
    {
        return $this->dateline;
    }

    /**
     * @param int $dateline
     * @return Postparsed
     */
    public function setDateline(int $dateline): self
    {
        $this->dateline = $dateline;

        return $this;
    }

    /**
     * @return int
     */
    public function getHasimages(): int
    {
        return $this->hasimages;
    }

    /**
     * @param int $hasimages
     * @return Postparsed
     */
    public function setHasimages(int $hasimages): self
    {
        $this->hasimages = $hasimages;

        return $this;
    }

    /**
     * @return string
     */
    public function getPagetextHtml(): string
    {
        return $this->pagetextHtml;
    }

    /**
     * @param string $pagetextHtml
     * @return Postparsed
     */
    public function setPagetextHtml(string $pagetextHtml): self
    {
        $this->pagetextHtml = $pagetextHtml;

        return $this;
    }

}

