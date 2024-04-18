import React, { Component, useEffect, useState } from 'react';
import Head from '../Components/Head';
import Style from '../Styles/Home.module.css';
import Article from '../Components/Article';

/**
 * @return
 * @constructor
 */
export default function Home(): JSX.Element {


    return (

        <>
            <div className={Style.containers}>

                <Article />

            </div>
        </>
    );
}