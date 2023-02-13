<?php 

namespace Blog\Http\Auth;
use Blog\Exceptions\AuthException;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\Request;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function user(Request $request): User
    {
        try{
            $userUuid = new UUID($request->jsonBodyField('author_uuid'));
        }catch(HttpException | \InvalidArgumentException $error){
            throw new AuthException($error->getMessage());
        }

        try{
            return $this->usersRepository->get($userUuid);
        }catch (UserNotFoundException $error){
            throw new AuthException($error->getMessage());
        }
    }
}