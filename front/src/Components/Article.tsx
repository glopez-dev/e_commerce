import React, { useState, useEffect } from 'react';
import axios from 'axios';

import Style from '../Styles/Article.module.css';
import Card from './Card';

interface Article {
    name: string;
    photo: string;
    id: number;

}

const Article = () => {
    const [articles, setArticles] = useState<Article[]>([]);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/products');
                setArticles(response.data);
                console.log(response.data);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        };

        fetchData();
    }, []);

    return (
        <div className={Style.containers}>
            <div className={Style.title}>
                <h1>LE TEMPS CHANGE D'ALLURE</h1>
                <p>Ode à la simplicité, la montre mécanique Hermès Cut affirme un style puissant.</p>
            </div>
            <div className={Style.article}>
                <div className={Style.card}>

                    {articles && articles.map((articles, index) => (
                        <Card key={index} image={articles.photo} title={articles.name} description={articles.id} />
                    ))}
                </div>
            </div>
        </div>
    );
};

export default Article;
