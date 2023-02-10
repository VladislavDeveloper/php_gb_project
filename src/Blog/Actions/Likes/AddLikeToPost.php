<?php

namespace Blog\Actions\Likes;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\AlreadyLikedException;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\PostNotFoundException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Like\Like;
use Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\UUID\UUID;
use InvalidArgumentException;

class AddLikeToPost implements ActionInterface
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postsRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try{
            $post_uuid = new UUID($request->jsonBodyField('post_uuid'));
            $author_uuid = new UUID($request->jsonBodyField('author_uuid'));
        }catch(HttpException | InvalidArgumentException $error){
            return new ErrorResponse($error->getMessage());
        }

        try{
            $this->postsRepository->get(new UUID($post_uuid));
        }catch(PostNotFoundException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Проверяем что пользователь ставит лайк первый раз
        try{
            $this->likesRepository->checkUserAlreadyLikedPost($post_uuid, $author_uuid);
        }catch(AlreadyLikedException $error){
            return new ErrorResponse($error->getMessage());
        } 

        try{
            $like = new Like(
                new UUID(UUID::random()),
                $post_uuid,
                $author_uuid
            );
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }

        $this->likesRepository->save($like);

        return new SuccessfulResponse([
            'message' => 'like successful !'
        ]);
    }

}