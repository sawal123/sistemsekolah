<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active', 'last_login_at', 'staff_position', 'unit_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'unit_id' => 'integer',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin')
            || ($this->hasRole('admin') && $this->unit_id === null);
    }

    public function isUnitAdmin(): bool
    {
        return ! $this->isSuperAdmin()
            && $this->hasAnyRole(['admin_unit', 'admin'])
            && $this->unit_id !== null;
    }

    public function isAnyAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isUnitAdmin();
    }

    public function effectiveUnitId(): ?int
    {
        if ($this->unit_id !== null) {
            return (int) $this->unit_id;
        }

        if ($this->relationLoaded('guru') && $this->guru?->unit_id) {
            return (int) $this->guru->unit_id;
        }

        if ($this->relationLoaded('siswa') && $this->siswa?->unit_id) {
            return (int) $this->siswa->unit_id;
        }

        return $this->guru()->value('unit_id') ?? $this->siswa()->value('unit_id');
    }

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    // Relasi ke Guru
    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function aiChatConversations()
    {
        return $this->hasMany(AiChatConversation::class);
    }

    public function submittedPaymentSubmissions()
    {
        return $this->hasMany(PaymentSubmission::class, 'submitted_by');
    }
}
