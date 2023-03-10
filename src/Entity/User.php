<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use App\Entity\Contract\HasMetaTimestampsInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JsonException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;



#[ORM\Table(name: '`user`')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements HasMetaTimestampsInterface, UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Column(name: 'id', type: 'bigint', unique:true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 32, unique: true, nullable: false)]
    private string $login;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $updatedAt;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: 'Tweet')]
    private Collection $tweets;

    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'followers')]
    private Collection $authors;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'authors')]
    #[ORM\JoinTable(name: 'author_follower')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'follower_id', referencedColumnName: 'id')]
    private Collection $followers;

    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: 'Subscription')]
    private Collection $subscriptionAuthors;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: 'Subscription')]
    private Collection $subscriptionFollowers;


    #[ORM\Column(type: 'string', length: 120, nullable: false)]
    private string $password;


    #[ORM\Column(type: 'integer', nullable: false)]
    private int $age;


    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $isActive;


    #[ORM\Column(type: 'string', length: 1024, nullable: false)]
    private string $roles = '{}';


    public function __construct()
    {
        $this->tweets = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->subscriptionAuthors = new ArrayCollection();
        $this->subscriptionFollowers = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }


//    #[ORM\PrePersist]
    public function setCreatedAt(): void {
        $this->createdAt = new DateTime();
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }


//    #[ORM\PrePersist]
//    #[ORM\PreUpdate]
    public function setUpdatedAt(): void {
        $this->updatedAt = new DateTime();
    }



    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'login'     => $this->login,
            'password'  => $this->password,
            'roles'     => $this->getRoles(),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'tweets'    => array_map(static fn(Tweet $tweet) => $tweet->toArray(), $this->tweets->toArray()),
            'followers' => array_map(
                static fn(User $user) => ['id' => $user->getId(), 'login' => $user->getLogin()],
                $this->followers->toArray()
            ),
            'authors' => array_map(
                static fn(User $user) => ['id' => $user->getLogin(), 'login' => $user->getLogin()],
                $this->authors->toArray()
            ),
            'subscriptionFollowers' => array_map(
                static fn(Subscription $subscription) => [
                    'subscriptionId' => $subscription->getId(),
                    'userId' => $subscription->getFollower()->getId(),
                    'login' => $subscription->getFollower()->getLogin(),
                ],
                $this->subscriptionFollowers->toArray()
            ),
            'subscriptionAuthors' => array_map(
                static fn(Subscription $subscription) => [
                    'subscriptionId' => $subscription->getId(),
                    'userId' => $subscription->getAuthor()->getId(),
                    'login' => $subscription->getAuthor()->getLogin(),
                ],
                $this->subscriptionAuthors->toArray()
            ),
        ];
    }

    public function addTweet(Tweet $tweet): void
    {
        if (!$this->tweets->contains($tweet)) {
            $this->tweets->add($tweet);
        }
    }

    public function addFollower(User $follower): void
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }
    }



    public function removeFollower(User $follower)
    {
        if ($this->followers->contains($follower)) {
            $this->followers->remove($follower);
        }
    }




    public function addAuthor(User $author): void
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }
    }

    public function addSubscriptionAuthor(Subscription $subscription): void
    {
        if (!$this->subscriptionAuthors->contains($subscription)) {
            $this->subscriptionAuthors->add($subscription);
        }
    }

    public function addSubscriptionFollower(Subscription $subscription): void
    {
        if (!$this->subscriptionFollowers->contains($subscription)) {
            $this->subscriptionFollowers->add($subscription);
        }
    }



    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }


    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }


    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }


    /**
     * @param string $isActive
     */
    public function setIsActive(string $isActive): void
    {
        $this->isActive = $isActive;
    }


    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }


    /**
     * @return array
    */
    public function getFollowers(): array
    {
        return $this->followers->toArray();
    }



    public function resetFollowers(): void
    {
        $this->followers = new ArrayCollection();
    }


    /**
     * @return string[]
     *
     * @throws JsonException
    */
    public function getRoles(): array
    {
        $roles = json_decode($this->roles, true, 512, JSON_THROW_ON_ERROR);

         // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }




    /**
     * @param string[] $roles
     *
     * @throws JsonException
    */
    public function setRoles(array $roles): void
    {
        $this->roles = json_encode($roles, JSON_THROW_ON_ERROR);
    }



    public function getSalt()
    {
         return null;
    }



    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        return $this->login;
    }


    public function getUserIdentifier(): string
    {
         return $this->login;
    }
}