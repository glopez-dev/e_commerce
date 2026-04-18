import React, { useState } from 'react';
import Style from '../Styles/AddArticle.module.css';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import axios from 'axios';
import {useAuth} from "../Components/Authentication/AuthProvider";

interface Article {
    name: string;
    description: string;
    price: number;
    photo: string;
}

const AddArticle: React.FC = () => {
    const [formData, setFormData] = useState<Article>({
        name: '',
        description: '',
        price: 0,
        photo: ''
    });
    const authProvider = useAuth();
    const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = event.target;
        setFormData({ ...formData, [name]: value });
    };

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        try {
            const response = await axios.post('http://127.0.0.1:8000/api/products', formData,
                {
                    headers: {
                        Authorization: `Bearer ${authProvider.getToken()}`,
                    },
                });
            console.log('Response:', response);
            console.log('Article ajouté avec succès !');
        } catch (error) {
            console.error('Erreur lors de l\'ajout de l\'article :', error);
        }
    };

    return (
        <div className={Style.containers}>
            <div className={Style.box}>
                <div className={Style.title}>
                    <h1>Formulaire d'ajout d'article</h1>
                </div>
                <form className={Style.form} onSubmit={handleSubmit}>
                    <p className={Style.text}>Name</p>
                    <TextField id="name" name="name" label="Name" variant="outlined" size="small" fullWidth onChange={handleChange} />

                    <p className={Style.text}>Description</p>
                    <TextField id="description" name="description" label="Description" variant="outlined" size="small" fullWidth onChange={handleChange} />

                    <p className={Style.text}>Price</p>
                    <TextField id="price" name="price" label="Price" variant="outlined" size="small" type="number" fullWidth onChange={handleChange} />

                    <p className={Style.text}>Photo</p>
                    <TextField id="photo" name="photo" label="Photo" variant="outlined" size="small" fullWidth onChange={handleChange} />

                    <Button type="submit" variant="text" fullWidth style={{ color: 'black' }}>
                        Ajouter un Article
                    </Button>
                </form>
            </div>
        </div>
    );
};

export default AddArticle;
