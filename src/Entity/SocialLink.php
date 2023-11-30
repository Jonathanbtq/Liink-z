<?php

namespace App\Entity;

use App\Repository\SocialLinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialLinkRepository::class)]
class SocialLink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'socialLinks')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $snapchat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagram = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtube = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitch = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebook = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tiktok = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $whatsapp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $amazon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $applemusic = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $discord = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $github = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kick = null;

    #[ORM\Column(length: 255)]
    private ?string $etsy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $patreon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $printerest = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spotify = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telegram = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSnapchat(): ?string
    {
        return $this->snapchat;
    }

    public function setSnapchat(?string $snapchat): self
    {
        $this->snapchat = $snapchat;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getTwitch(): ?string
    {
        return $this->twitch;
    }

    public function setTwitch(?string $twitch): self
    {
        $this->twitch = $twitch;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getTiktok(): ?string
    {
        return $this->tiktok;
    }

    public function setTiktok(?string $tiktok): self
    {
        $this->tiktok = $tiktok;

        return $this;
    }

    public function getWhatsapp(): ?string
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(?string $whatsapp): self
    {
        $this->whatsapp = $whatsapp;

        return $this;
    }

    public function getAmazon(): ?string
    {
        return $this->amazon;
    }

    public function setAmazon(?string $amazon): self
    {
        $this->amazon = $amazon;

        return $this;
    }

    public function getApplemusic(): ?string
    {
        return $this->applemusic;
    }

    public function setApplemusic(?string $applemusic): self
    {
        $this->applemusic = $applemusic;

        return $this;
    }

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(?string $discord): self
    {
        $this->discord = $discord;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): self
    {
        $this->github = $github;

        return $this;
    }

    public function getKick(): ?string
    {
        return $this->kick;
    }

    public function setKick(?string $kick): self
    {
        $this->kick = $kick;

        return $this;
    }

    public function getEtsy(): ?string
    {
        return $this->etsy;
    }

    public function setEtsy(string $etsy): self
    {
        $this->etsy = $etsy;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getPatreon(): ?string
    {
        return $this->patreon;
    }

    public function setPatreon(?string $patreon): self
    {
        $this->patreon = $patreon;

        return $this;
    }

    public function getPrinterest(): ?string
    {
        return $this->printerest;
    }

    public function setPrinterest(?string $printerest): self
    {
        $this->printerest = $printerest;

        return $this;
    }

    public function getSpotify(): ?string
    {
        return $this->spotify;
    }

    public function setSpotify(?string $spotify): self
    {
        $this->spotify = $spotify;

        return $this;
    }

    public function getTelegram(): ?string
    {
        return $this->telegram;
    }

    public function setTelegram(?string $telegram): self
    {
        $this->telegram = $telegram;

        return $this;
    }
}
