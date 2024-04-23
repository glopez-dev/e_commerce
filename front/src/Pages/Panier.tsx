import React, { useEffect, useState } from 'react';
import Style from '../Styles/Panier.module.css';
import axios from 'axios';
import ArticlePanier from '../Components/ArticlePanier';
import Button from '@mui/material/Button';

const Paniers = () => {

    interface Pokemon {
        name: string;
        url: string;
        id: number;

    }

    const [pokemons, setPokemons] = useState<Pokemon[]>([]);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get('https://pokeapi.co/api/v2/pokemon?limit=151');
                setPokemons(response.data.results);
                console.log(response.data.results);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        };

        fetchData();
    }, []);
    return (
        <div className={Style.containers}>
            <div className={Style.box}>
                <div className={Style.title}>
                    <p>Vous avez {pokemons.length} objets dans votre panier.</p>
                </div>

                <div>
                    {pokemons.map((pokemon, index) => (
                        <ArticlePanier key={index} name={pokemon.name} commentaire="C'est la pièce la plus droçée de tout le temps !" />
                    ))}
                </div>
            </div>



            <div className={Style.box2}>
                <div className={Style.cardBuy}>
                    <div className={Style.title}>
                        <p>Vous voulez acheter les {pokemons.length} </p>
                    </div>



                    <Button style={{ color: 'black', backgroundColor: '#f6f1eb', width: '100%' }} variant="text">Acheter</Button>




                </div>
            </div>


        </div>
    );
};

export default Paniers;