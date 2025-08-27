<form class="space-y-3" action="{{ route('admin.general-settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="types[]" value="primaryColor">
    <x-backend.inputs.color label="Primary Color" name="primaryColor" value="{{ getSetting('primaryColor') }}" />

    <input type="hidden" name="types[]" value="websiteHeaderLogo">
    <x-backend.inputs.file label="Header Logo" name="websiteHeaderLogo" placeholder="Website header logo"
        value="{{ getSetting('websiteHeaderLogo') }}" />

    <input type="hidden" name="types[]" value="sellerDashboardLogo">
    <x-backend.inputs.file label="Seller Dashboard Logo" name="sellerDashboardLogo"
        value="{{ getSetting('sellerDashboardLogo') }}" />

    <input type="hidden" name="types[]" value="websiteFooterLogo">
    <x-backend.inputs.file label="Footer Logo" name="websiteFooterLogo" placeholder="Website footer logo"
        value="{{ getSetting('websiteFooterLogo') }}" />


    <input type="hidden" name="types[]" value="favicon">
    <x-backend.inputs.file label="Favicon" name="favicon" placeholder="System favicon"
        value="{{ getSetting('favicon') }}" />

    <input type="hidden" name="types[]" value="poweredBy">
    <x-backend.inputs.file label="Powered by" name="poweredBy" placeholder="Dashboard powered by"
        value="{{ getSetting('poweredBy') }}" />

    <div class="flex justify-end pt-2">
        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
    </div>
</form>
