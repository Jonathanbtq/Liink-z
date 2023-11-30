<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'There is already an account with this id')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    public ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    public ?string $email = null;

    #[ORM\Column]
    private ?bool $certified = null;

    #[ORM\OneToMany(mappedBy: 'subscription_user', targetEntity: Subscription::class)]
    private Collection $subscriptions;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Links::class, orphanRemoval: true)]
    private Collection $link;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Links::class)]
    private Collection $links;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profile_img = null;

    #[ORM\Column]
    private ?bool $subscribe_accept = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_back = null;

    #[ORM\Column]
    private ?bool $token_validation = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Token::class)]
    private Collection $tokens;

    #[ORM\Column(length: 10)]
    private ?string $colorCustom = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SocialLink::class)]
    private Collection $socialLinks;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->link = new ArrayCollection();
        $this->links = new ArrayCollection();
        $this->tokens = new ArrayCollection();
        $this->socialLinks = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isCertified(): ?bool
    {
        return $this->certified;
    }

    public function setCertified(bool $certified): self
    {
        $this->certified = $certified;

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setSubscriptionUser($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getSubscriptionUser() === $this) {
                $subscription->setSubscriptionUser(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Links>
     */
    public function getLink(): Collection
    {
        return $this->link;
    }

    public function addLink(Links $link): self
    {
        if (!$this->link->contains($link)) {
            $this->link->add($link);
            $link->setUser($this);
        }

        return $this;
    }

    public function removeLink(Links $link): self
    {
        if ($this->link->removeElement($link)) {
            // set the owning side to null (unless already changed)
            if ($link->getUser() === $this) {
                $link->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Links>
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function getProfileImg(): ?string
    {
        return $this->profile_img;
    }

    public function setProfileImg(?string $profile_img): self
    {
        $this->profile_img = $profile_img;

        return $this;
    }

    /******
     * Permet de pouvoir accepter l'abonnement au compte
     */
    public function isSubscribeAccept(): ?bool
    {
        return $this->subscribe_accept;
    }

    public function setSubscribeAccept(bool $subscribe_accept): self
    {
        $this->subscribe_accept = $subscribe_accept;

        return $this;
    }

    public function getImageBack(): ?string
    {
        return $this->image_back;
    }

    public function setImageBack(?string $image_back): self
    {
        $this->image_back = $image_back;

        return $this;
    }

    public function isTokenValidation(): ?bool
    {
        return $this->token_validation;
    }

    public function setTokenValidation(bool $token_validation): self
    {
        $this->token_validation = $token_validation;

        return $this;
    }

    /**
     * @return Collection<int, Token>
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens->add($token);
            $token->setUser($this);
        }

        return $this;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->removeElement($token)) {
            // set the owning side to null (unless already changed)
            if ($token->getUser() === $this) {
                $token->setUser(null);
            }
        }

        return $this;
    }

    public function getColorCustom(): ?string
    {
        return $this->colorCustom;
    }

    public function setColorCustom(string $colorCustom): self
    {
        $this->colorCustom = $colorCustom;

        return $this;
    }

    /**
     * @return Collection<int, SocialLink>
     */
    public function getSocialLinks(): Collection
    {
        return $this->socialLinks;
    }

    public function addSocialLink(SocialLink $socialLink): self
    {
        if (!$this->socialLinks->contains($socialLink)) {
            $this->socialLinks->add($socialLink);
            $socialLink->setUser($this);
        }

        return $this;
    }

    public function removeSocialLink(SocialLink $socialLink): self
    {
        if ($this->socialLinks->removeElement($socialLink)) {
            // set the owning side to null (unless already changed)
            if ($socialLink->getUser() === $this) {
                $socialLink->setUser(null);
            }
        }

        return $this;
    }
}
