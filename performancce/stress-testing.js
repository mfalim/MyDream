
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    stages: [
        { duration: '30s', target: 10 },
        { duration: '30s', target: 25 },
        { duration: '30s', target: 50 },
        { duration: '30s', target: 75 },
        { duration: '30s', target: 100 },
        { duration: '30s', target: 125 },
        { duration: '30s', target: 150 },
        { duration: '30s', target: 0 },
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
