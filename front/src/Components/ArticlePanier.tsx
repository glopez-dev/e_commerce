import React from 'react';
import Style from '../Styles/Panier.module.css';

interface ArticleProps {
    name: string;
    description: string;
    photo: string;
    price: number;


}

const ArticleComponent: React.FC<ArticleProps> = ({ name, description, photo, price }) => {
    return (

        <div className={Style.article}>
            <div className={Style.box1}>
                <div className={Style.img}>{photo}</div>
                <div className={Style.detail}>
                    <p>{name}</p>
                    <p>{description}</p>

                </div>
            </div>
        </div>
    );
};

export default ArticleComponent;
