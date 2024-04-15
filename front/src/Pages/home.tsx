import React, {Component, useEffect, useState} from 'react';
import Head from '../Components/Head';

/**
 * @return
 * @constructor
 */
export default function Home() : JSX.Element {
    const [page, setPage] = useState(null);
    useEffect(() => {
        const fetchTest = async () => {
            const response = await fetch("http://localhost:8000/api/test");
            const json = await response.json();
            if (response.ok) {
                setPage(json);
            }
        }
        fetchTest();
    },[]);

    return (
        <>
            <div>
                <p>{page && console.log(page)}</p>
            </div>
        </>
    );
}