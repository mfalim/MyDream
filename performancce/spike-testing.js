
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    stages: [
        { duration: '1m', target: 10 },
        { duration: '5s', target: 150 },
        { duration: '1m', target: 150 },
        { duration: '5s', target: 10 },
        { duration: '1m', target: 10 },
        { duration: '5s', target: 0 },
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
