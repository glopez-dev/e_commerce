import React, { Component, useEffect, useState } from 'react';
import Head from '../Components/Head';
import Style from '../Styles/Home.module.css';

/**
 * @return
 * @constructor
 */
export default function Home(): JSX.Element {
    // const [page, setPage] = useState(null);
    // useEffect(() => {
    //     const fetchTest = async () => {
    //         const response = await fetch("http://localhost:8000/api/test");
    //         const json = await response.json();
    //         if (response.ok) {
    //             setPage(json);
    //         }
    //     }
    //     fetchTest();
    // }, []);

    return (

        <>
            <div className={Style.containers}>
                <Head />

            </div>
        </>
    );
}