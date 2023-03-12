<?php
namespace App\Security\Authenticator\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method string getUserIdentifier()
 */
class AuthUser implements UserInterface
{

    private string $username;


    /** @var string[] */
    private array $roles;



    public function __construct(array $credentials)
    {
        $this->username = $credentials['username'];
        $this->roles    = array_unique(array_merge($credentials['roles'] ?? [], ['ROLE_USER']));
    }


    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return '';
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
         return $this->username;
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }
}