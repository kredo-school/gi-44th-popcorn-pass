<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSimilarity extends Model
{
    protected $table = 'user_similarities';

    protected $fillable = [
        'user_id_1',
        'user_id_2',
        'similarity_score',
    ];

    protected $casts = [
        'similarity_score' => 'float',
    ];

    // Relation: User 1
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_id_1', 'id');
    }

    // Relation: User 2
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_id_2', 'id');
    }

    /**
     * Get similar users for a given user (both directions)
     */
    public static function getSimilarUsers($userId, $minSimilarity = 0.3)
    {
        return self::where(function ($query) use ($userId) {
            $query->where('user_id_1', $userId)
                  ->orWhere('user_id_2', $userId);
        })
        ->where('similarity_score', '>=', $minSimilarity)
        ->orderBy('similarity_score', 'desc')
        ->get()
        ->map(function ($similarity) use ($userId) {
            return [
                'similar_user_id' => $similarity->user_id_1 === $userId ? $similarity->user_id_2 : $similarity->user_id_1,
                'similarity_score' => $similarity->similarity_score,
            ];
        });
    }
}