<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Todo extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'content', 'is_completed', 'priority', 'due_date'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeCategorySearch($query, $category_id)
    {
        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }
        return $query;
    }

    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('content', 'like', '%' . $keyword . '%');
        }
        return $query;
    }

    public function scopePrioritySearch($query, $priority)
    {
        if (!empty($priority)) {
            $query->where('priority', $priority);
        }
        return $query;
    }

    public function scopeDueDateSearch($query, $due_date)
    {
        if (!empty($due_date)) {
            $query->where('due_date', $due_date);
        }
        return $query;
    }

    public function scopeIsCompletedSearch($query, $is_completed)
    {
        if ($is_completed == '1') {
            $query->where('is_completed', 1);
        } elseif ($is_completed == '0') {
            $query->where('is_completed', 0);
        }
        return $query;
    }
}
