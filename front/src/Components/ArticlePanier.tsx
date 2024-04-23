import React from 'react';
import Style from '../Styles/Panier.module.css';

interface ArticleProps {
    name: string;
    commentaire: string;
}

const ArticleComponent: React.FC<ArticleProps> = ({ name, commentaire }) => {
    return (

        <div className={Style.article}>
            <div className={Style.box1}>
                <div className={Style.img}>Article</div>
                <div className={Style.detail}>
                    <p>{name}</p>
                    <p>{commentaire}</p>

                </div>
            </div>
        </div>
    );
};

export default ArticleComponent;
