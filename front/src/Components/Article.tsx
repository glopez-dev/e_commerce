import React from 'react';
import axios from 'axios';

import Style from '../Styles/Article.module.css';
import Card from './Card';

interface Pokemon {
    name: string;
    url: string;
    id: number;

}

export default function Article(): JSX.Element {
    const [pokemons, setPokemons] = React.useState<Pokemon[]>([]);

    React.useEffect(() => {
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
            <div className={Style.title}>
                <h1>LE TEMPS CHANGE D'ALLURE</h1>
                <p>Ode à la simplicité, la montre mécanique Hermès Cut affirme un style puissant.</p>
            </div>
            <div className={Style.article}>
                <div className={Style.card}>
                    {pokemons.map((pokemon, index) => (
                        <Card key={index} image={pokemon.url} title={pokemon.name} description={pokemon.id} />
                    ))}
                </div>
            </div>
        </div>
    );
};
