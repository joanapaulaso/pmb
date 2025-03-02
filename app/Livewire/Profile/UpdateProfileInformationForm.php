<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Profile;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * The profile data.
     *
     * @var array
     */
    public $profileData = [];

    /**
     * The new avatar for the user.
     *
     * @var mixed
     */
    public $photo;

    /**
     * Determine if the verification email was sent.
     *
     * @var bool
     */
    public $verificationLinkSent = false;

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {
        $this->state = Auth::user()->withoutRelations()->toArray();

        // Carregar dados do perfil
        $profile = Profile::where('user_id', Auth::user()->id)->first();
        if ($profile) {
            $this->profileData = [
                'gender' => $profile->gender,
                'birth_date' => $profile->birth_date,
            ];
        } else {
            $this->profileData = [
                'gender' => null,
                'birth_date' => null,
            ];
        }
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return void
     */
    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        $this->resetErrorBag();

        $updater->update(
            Auth::user(),
            $this->photo
                ? array_merge($this->state, ['photo' => $this->photo])
                : $this->state
        );

        // Atualizar ou criar perfil
        $profile = Profile::where('user_id', Auth::user()->id)->first();
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = Auth::user()->id;
        }

        // Atualizar campos do perfil
        $profile->gender = $this->profileData['gender'] ?? null;
        $profile->birth_date = $this->profileData['birth_date'] ?? null;
        $profile->save();

        if (isset($this->photo)) {
            return redirect()->route('profile.show');
        }

        $this->dispatch('saved');
    }

    /**
     * Delete user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto()
    {
        Auth::user()->deleteProfilePhoto();

        $this->dispatch('refresh-navigation-menu');
    }

    /**
     * Sent the email verification.
     *
     * @return void
     */
    public function sendEmailVerification()
    {
        Auth::user()->sendEmailVerificationNotification();

        $this->verificationLinkSent = true;
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('profile.update-profile-information-form');
    }
}
