
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    vus: 100,
    duration: '10s',
};

export default function () {
    const response = http.get(
        'http://127.0.0.1:8000/api/vendors'
    );

    console.log(`STATUS: ${response.status}`);
    console.log(`RESPONSE: ${response.body.substring(0, 300)}`);

    check(response, {
        'status code 200': (r) => r.status === 200,
    });

    sleep(1);
}
