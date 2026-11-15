<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## JSON API

The JSON API is available under `/api`. Public catalog and published-content reads do not require authentication. Send `Accept: application/json` for JSON validation and error responses.

- Public reads: `GET /home`, `/about`, `/store`, `/catalog`, `/catalog/categories`, `/catalog/packages`, `/catalog/packages/{slug}`, `/catalog/vendors/{slug}`, `/vendors`, `/members`, `/content/articles`, `/content/events`, and `/content/inspirations` (detail routes use `{slug}`). `/about` returns the same title, description, hero, story, values, and CTA content used by the web page.
- Sign in with Google using `POST /auth/google` and an `id_token`; use the returned token as `Authorization: Bearer <token>`.
- Any signed-in user can call `GET /me` and `POST /logout`.
- Any signed-in user can use `/cart` (`GET`, `DELETE`), add a package with `POST /cart/packages/{id}`, add a custom vendor with `POST /cart/vendors/{id}`, and remove a vendor with `DELETE /cart/vendors/{id}`. API carts persist in the database, are isolated by user, and follow the web cart's package/custom selection rules.
- Admin endpoints require a Sanctum token and the `admin` role. Resource CRUD endpoints are `/admin/categories`, `/admin/packages`, `/admin/articles`, `/admin/public-events`, `/admin/organizer-events`, `/admin/members`, `/admin/member-divisions`, `/admin/member-positions`, and `/admin/member-specializations`. Vendor writes retain the existing `/vendors` URLs and require the admin role. Event scheduling, calendar, and tracking operations are under `/admin/events`, `/admin/calendar/events`, and `/admin/tracking`.
- Client endpoints require the `client` role and cover profile, bookings/checkout, event planning, and booking payments under `/client`.
- Member endpoints require the `member` role and cover dashboard, profile completion, and assigned schedule status under `/member`.

Client checkout is submitted directly as `POST /client/bookings` with event/venue details and either a `package_id` or at least three `vendor_ids`. The bearer-token API cart is database-backed and separate from the existing web session cart.

## Midtrans Snap

The existing web checkout still creates the booking and opens its payment page. Payment is handled in a Snap popup, so customers do not leave MyDream. Add these sandbox settings to `.env` and replace the dummy values with the keys from your Midtrans dashboard:

```dotenv
MIDTRANS_SERVER_KEY=SB-Mid-server-DUMMY_REPLACE_WITH_YOUR_SERVER_KEY
MIDTRANS_CLIENT_KEY=SB-Mid-client-DUMMY_REPLACE_WITH_YOUR_CLIENT_KEY
MIDTRANS_IS_PRODUCTION=false
```

Authenticated client API users can also create a Snap transaction with `POST /api/client/bookings/{bookingId}/payments/midtrans`, sending `{"scheme":"dp"}`, `{"scheme":"full"}`, or `{"scheme":"remaining"}` as appropriate. The response includes a `token` for opening the Snap payment UI. The existing `POST /api/client/bookings/{bookingId}/payments` endpoint remains for bank-transfer submissions.

Set the Midtrans payment notification URL to `https://your-domain.example/api/midtrans/notification`. Only signed notifications from Midtrans update the booking payment status; the browser popup callbacks do not mark payments as paid. Set `MIDTRANS_IS_PRODUCTION=true` and use production keys when moving to production.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
