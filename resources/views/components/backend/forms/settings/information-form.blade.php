<form action="{{ route('admin.general-settings.update') }}" class="space-y-3" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="types[]" value="systemName">
    <x-backend.inputs.text label="System Name" name="systemName" placeholder="Type system name"
        value="{{ getSetting('systemName') }}" />

    <input type="hidden" name="types[]" value="systemTitle">
    <x-backend.inputs.text label="System Title" name="systemTitle" placeholder="Type system title"
        value="{{ getSetting('systemTitle') }}" />

    <input type="hidden" name="types[]" value="systemAddress">
    <x-backend.inputs.textarea label="System Address" name="systemAddress" placeholder="Type system address"
        value="{{ getSetting('systemAddress') }}" />

    <input type="hidden" name="types[]" value="systemPhone">
    <x-backend.inputs.textarea label="System Phone" name="systemPhone" placeholder="Type system phone"
        value="{{ getSetting('systemPhone') }}" />

    <input type="hidden" name="types[]" value="systemEmail">
    <x-backend.inputs.textarea label="System Email" name="systemEmail" placeholder="Type system email"
        value="{{ getSetting('systemEmail') }}" />

    <input type="hidden" name="types[]" value="currencySymbol">
    <x-backend.inputs.text label="Currency Symbol" name="currencySymbol" placeholder="Type surrency symbol"
        value="{{ getSetting('currencySymbol') }}" />

    <input type="hidden" name="types[]" value="currencyCode">
    <x-backend.inputs.text label="Currency Code" name="currencyCode" placeholder="Type surrency code"
        value="{{ getSetting('currencyCode') }}" />

    <input type="hidden" name="types[]" value="currencySymbolAlignment">
    <x-backend.inputs.select label="Currency Alignment" name="currencySymbolAlignment">
        <x-backend.inputs.select-option name="[symbol][amount] - $100" value="1"
            selected="{{ getSetting('currencySymbolAlignment') }}" />
        <x-backend.inputs.select-option name="[amount][symbol] - 100$" value="2"
            selected="{{ getSetting('currencySymbolAlignment') }}" />
        <x-backend.inputs.select-option name="[symbol] [amount] - Tk 100" value="3"
            selected="{{ getSetting('currencySymbolAlignment') }}" />
        <x-backend.inputs.select-option name="[amount] [symbol] - 100 Tk" value="4"
            selected="{{ getSetting('currencySymbolAlignment') }}" />
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="decimalSeparator">
    <x-backend.inputs.text label="Decimal Separator" name="decimalSeparator" placeholder="Add decimal separator"
        value="{{ getSetting('decimalSeparator') }}" />

    <input type="hidden" name="types[]" value="numOfDecimals">
    <x-backend.inputs.select label="Number Of Decimals" name="numOfDecimals">
        <x-backend.inputs.select-option name="Inactive - $100" value="0"
            selected="{{ getSetting('numOfDecimals') }}" />
        <x-backend.inputs.select-option name="1 Decimal - $100.0" value="1"
            selected="{{ getSetting('numOfDecimals') }}" />
        <x-backend.inputs.select-option name="2 Decimals - $100.00" value="2"
            selected="{{ getSetting('numOfDecimals') }}" />
        <x-backend.inputs.select-option name="3 Decimals - $100.000" value="3"
            selected="{{ getSetting('numOfDecimals') }}" />
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="thousandSeparator">
    <x-backend.inputs.text label="Thousand Separator" name="thousandSeparator" placeholder="Add thousand separator"
        value="{{ getSetting('thousandSeparator') }}" />

    <input type="hidden" name="types[]" value="emailVerification">
    <x-backend.inputs.select label="Account Verification" name="emailVerification" groupClass="">
        <x-backend.inputs.select-option name="Active" value="1"
            selected="{{ getSetting('emailVerification') }}" />
        <x-backend.inputs.select-option name="Inactive" value="0"
            selected="{{ getSetting('emailVerification') }}" />
    </x-backend.inputs.select>

    {{-- <input type="hidden" name="types[]" value="manageOrderBy"> --}}
    <x-backend.inputs.select label="Manage Order By" name="manageOrderBy" groupClass="hidden">
        <x-backend.inputs.select-option name="Admin & Each Shop" value="both"
            selected="{{ getSetting('manageOrderBy') }}" />
        <x-backend.inputs.select-option name="Only Admin" value="admin"
            selected="{{ getSetting('manageOrderBy') }}" />
        <x-backend.inputs.select-option name="Each Shop" value="shop"
            selected="{{ getSetting('manageOrderBy') }}" />
    </x-backend.inputs.select>

    {{-- <input type="hidden" name="types[]" value="appMode">
    <x-backend.inputs.select label="App Mode" name="appMode">
        <x-backend.inputs.select-option name="Single Vendor" value="singleVendor"
            selected="{{ config('app.app_mode') }}" />
        <x-backend.inputs.select-option name="Multi Vendor" value="multiVendor"
            selected="{{ config('app.app_mode') }}" />
    </x-backend.inputs.select> --}}

    <input type="hidden" name="types[]" value="timezone">
    <x-backend.inputs.select label="Timezone" name="timezone">
        @foreach (timezones() as $key => $value)
            <x-backend.inputs.select-option name="{{ $key }}" value="{!! $value !!}"
                selected="{{ config('app.timezone') }}" />
        @endforeach
    </x-backend.inputs.select>

    {{-- <input type="hidden" name="types[]" value="adminCommissionPercentage">
    <x-backend.inputs.text label="Admin Commission Rate From Shops" name="adminCommissionPercentage"
        placeholder="10.00" value="{{ getSetting('adminCommissionPercentage') }}" />

    <input type="hidden" name="types[]" value="minWithdrawalAmount">
    <x-backend.inputs.text label="Minimum Payout Amount" name="minWithdrawalAmount" placeholder="10.00"
        value="{{ getSetting('minWithdrawalAmount') }}" /> --}}

    <div class="flex justify-end pt-2">
        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
    </div>
</form>
