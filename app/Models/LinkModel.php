<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkModel extends Model
{
    protected $table = 'links';
    protected $allowedFields = ['original_url', 'short_code'];
    protected $useTimestamps = true;
}
