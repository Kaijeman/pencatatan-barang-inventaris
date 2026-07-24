<tr>
    <td class="header">
        <a
            href="{{ $url }}"
            style="
                display: inline-block;
                text-decoration: none;
            "
        >
            {{-- Logo aplikasi. --}}
            @if (config('mail.logo_url'))
                <img
                    src="{{ config('mail.logo_url') }}"
                    alt="{{ config('app.name') }}"
                    width="72"
                    style="
                        display: block;
                        width: 72px;
                        max-width: 72px;
                        height: auto;
                        margin: 0 auto 12px;
                        border: 0;
                        outline: none;
                        object-fit: contain;
                    "
                >
            @endif

            {{-- Nama aplikasi. --}}
            <span
                style="
                    display: block;
                    color: #1e293b;
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 20px;
                    font-weight: 700;
                    line-height: 1.4;
                    text-align: center;
                "
            >
                {{ config('app.name') }}
            </span>
        </a>
    </td>
</tr>
