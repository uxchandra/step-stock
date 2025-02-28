<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'name'           => $row['nama'],
            'username'       => $row['username'],
            'password'       => Hash::make($row['password']),
            'role_id'        => $row['role_id'],
            'department_id'  => $row['department_id']
        ]);
    }
}
