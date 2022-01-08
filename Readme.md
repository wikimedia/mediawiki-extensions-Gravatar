# Gravatar
Gravatar is an extension that provides avatars based on the user's email address. It supports [gravatar.com](https://www.gravatar.com) or [libravatar.org](https://www.libravatar.org), including self-hosted libravatar instances.

## Supported skins
 * Vector
 * Timeless
 * MonoBook
 * Mirage
 * Minerva Neue
 * Any skin that provides an element with the classes `ext-gravatar-avatar ext-gravatar-user-avatar`.

## Privacy concerns
The use of gravatar avatars is guarded by the `gravatar-use-gravatar` preference. Users have to explicitly opt-in to allow their email address to be used for retrieving an avatar.

Site administrators should **not** set `gravatar-use-gravatar` to `true` in `$wgDefaultUserOptions`, **unless** users have non-personal email addresses set, or a gravatar proxy is active.
In that case, disabling the user preference through `$wgHiddenPrefs` might be desirable, as the Gravatar privacy policy no longer applies.

## Configuration
 - `$wgGravatarServer` - This setting specifies the avatar service to connect to. By default, this is [//gravatar.com](https://www.gravatar.com).
 - `$wgGravatarDefaultAvatar` - This setting controls the default avatar for users who have not opted-in or have no valid email address set. It can either be the url to an image, or one of the keywords supported by gravatar.
 - `$wgGravatarAcceptedAvatarRating` - This setting controls the rating that avatars should have.
 - `$wgGravatarIgnoredSkins` - This setting allows excluding certain skins from having the user's avatar displayed in the interface.
