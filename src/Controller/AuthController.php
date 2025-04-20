<?php

namespace App\Controller;


use App\Entity\User;
use App\Model\RegisterUserModel;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

class AuthController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private UserManager $userManager
    ) {}

    #[Route('/account', name: 'create_account', methods: ['POST'])]
    #[OA\Post(
        summary: 'Register a new user account',
        tags: ['Auth'],  // Custom tag for AuthController
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: RegisterUserModel::class))
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created successfully'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 409, description: 'Email already in use'),
            new OA\Response(response: 422, description: 'Unexpected error')
        ]
    )]
    public function register(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $model = $this->serializer->denormalize($data, RegisterUserModel::class);

        $errors = $this->validator->validate($model);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], 400);
        }

        $existing = $em->getRepository(User::class)->findOneBy(['email' => $model->email]);
        if ($existing) {
            return new JsonResponse(['error' => 'Email already in use'], 409);
        }

        try {
            $user = $this->userManager->registerFromModel($model);

            return new JsonResponse([
                'message' => 'Account successfully created',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'username' => $user->getUsername(),
                    'firstname' => $user->getFirstname()
                ]
            ], 201);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Unexpected error'], 422);
        }
    }

    #[Route('/token', name: 'auth_token', methods: ['POST'])]
    #[OA\Post(
        summary: 'Login and generate JWT token',
        tags: ['Auth'],  // Custom tag for AuthController
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Successfully authenticated, JWT token returned'),
            new OA\Response(response: 400, description: 'Missing credentials'),
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 422, description: 'Unexpected error')
        ]
    )]
    public function login(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Missing credentials'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (!$user || !$hasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $exp = (int) $this->getParameter('app.jwt_exp') ?? 86400;
        $secret = $this->getParameter('app.jwt_secret');
        $algo = $this->getParameter('app.jwt_algo') ?? 'HS256';

        $token = JWT::encode([
            'user' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'id' => $user->getId(),
            'iat' => time(),
            'exp' => time() + $exp,
        ], $secret, $algo);

        return new JsonResponse([
            'token' => $token,
            'expires_in' => $exp
        ]);
    }
}
