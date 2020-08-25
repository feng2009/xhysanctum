<?php

namespace Xiaohuyun\xhysanctum;

use Illuminate\Support\Str;

trait HasApiTokens
{
    /**
     * The access token the user is using for the current request.
     *
     * @var \Xiaohuyun\xhysanctum\Contracts\HasAbilities
     */
    protected $accessToken;

    /**
     * Get the access tokens that belong to model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tokens($id = 'id')
    {
        $id=$id;
        return $this->morphMany(Sanctum::$personalAccessTokenModel, 'tokenable',null,null,$id);
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param  string  $ability
     * @return bool
     */
    public function tokenCan(string $ability)
    {
        return $this->accessToken ? $this->accessToken->can($ability) : false;
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  string  $id
     * @param  array  $abilities
     * @return \Xiaohuyun\xhysanctum\NewAccessToken
     */
    public function createToken(string $name, string $id='id',array $abilities = ['*'])
    {
        $token = $this->tokens($id)->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(80)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $token->id.'|'.$plainTextToken);
    }

    /**
     * Get the access token currently associated with the user.
     *
     * @return \Xiaohuyun\xhysanctum\Contracts\HasAbilities
     */
    public function currentAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the current access token for the user.
     *
     * @param  \Xiaohuyun\xhysanctum\Contracts\HasAbilities  $accessToken
     * @return $this
     */
    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
