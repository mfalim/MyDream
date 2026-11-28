<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode Verifikasi MyDream</title>
</head>
<body style="margin:0;padding:24px;background-color:#FBF3EC;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#2B211C;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table role="presentation" width="520" style="max-width:100%;background:#FFFFFF;border:1px solid #E8D8CC;border-radius:20px;overflow:hidden;">
                    <tr>
                        <td style="background:#5C1620;padding:24px 28px;text-align:center;">
                            <div style="font-family:Georgia,serif;font-size:26px;color:#FFFFFF;letter-spacing:0.5px;">MyDream</div>
                            <div style="font-size:11px;letter-spacing:3px;text-transform:uppercase;color:#E2BE82;margin-top:4px;">Wedding Organizer</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 28px;text-align:center;">
                            <div style="font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#B08D57;">
                                {{ $purposeLabel }}
                            </div>
                            <h2 style="margin:8px 0 12px;font-family:Georgia,serif;font-size:22px;color:#2B211C;">
                                Kode Verifikasi OTP Anda
                            </h2>
                            <p style="margin:0 0 22px;font-size:14px;line-height:1.6;color:#6B5B53;">
                                Masukkan 6 digit kode berikut untuk melanjutkan proses <strong>{{ strtolower($purposeLabel) }}</strong> pada akun MyDream Anda:
                            </p>

                            <div style="display:inline-block;padding:16px 32px;border-radius:14px;background:#FCF8F4;border:1px solid #E8D8CC;font-size:32px;font-weight:700;letter-spacing:8px;color:#7A1F2B;">
                                {{ $code }}
                            </div>

                            <p style="margin:22px 0 0;font-size:12.5px;line-height:1.6;color:#7A6760;">
                                Kode ini hanya berlaku selama <strong>{{ $ttlMinutes }} menit</strong> dan bersifat rahasia.<br>
                                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
