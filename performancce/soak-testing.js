
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    stages: [
        { duration: '1m', target: 50 },
        { duration: '30m', target: 50 },
        { duration: '1m', target: 0 },
    ],
};

export default function () {
    const response = http.get(
        'http://127.0.0.1:8000/api/vendors'
    );

    check(response, {
        'status code 200': (r) => r.status === 200,
    });

    sleep(1);
}
