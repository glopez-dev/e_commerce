import React, {useEffect, useState} from 'react';
import Style from '../Styles/Register.module.css';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import {Link} from 'react-router-dom';
import axios from 'axios';
import {Simulate} from "react-dom/test-utils";
import error = Simulate.error;
import {useAuth} from "../Components/Authentication/AuthProvider";

const Register: React.FC = () => {
    const [email, setEmail] = useState<string>('');
    const [password, setPassword] = useState<string>('');
    const [firstname, setFirstame] = useState<string>('');
    const [lastname, setLastname] = useState<string>('');
    const [confirmPassword, setConfirmPassword] = useState<string>('');
    const [login, setLogin] = useState<string>('');
    const [passwordError, setPasswordError] = useState<boolean>(false)
    const authProvider = useAuth();

    const handleRegister = async (e: React.FormEvent) => {
        e.preventDefault();
        if (password !== confirmPassword) {
            setPasswordError(false);
            return passwordError;
        }
        try {
            const response = await axios.post('http://localhost:8000/api/register', {
                email,
                password,
                login,
                firstname,
                lastname
            });
            const data = response.data;
            if (response.status === 201) {
                authProvider.onLogin(data.token);
            }
            setEmail('');
            setPassword('');
            setLastname('');
            setFirstame('');
            setLogin('');
            setConfirmPassword('');
        } catch (error) {
            console.error('Register failed:', error);
        }
    };

    return (
        <div className={Style.containers}>
            <div className={Style.box}>
                <div className={Style.title}>
                    <h1>Votre compte</h1>
                </div>
                <div className={Style.login}>
                    <div className={Style.card}>
                        <div className={Style.p}>
                            <p className={Style.text}>
                                Veuillez ajouter votre e-mail ainsi que votre mot de passe
                                ci-dessous pour accéder à votre compte ou créer un compte
                            </p>
                        </div>
                        <form onSubmit={(e) => handleRegister(e)}>
                            <div className={Style.form}>
                                <div className={Style.inputGroup}>
                                    <TextField
                                        id="firstname"
                                        label="Prénom"
                                        variant="outlined"
                                        value={firstname}
                                        onChange={(e) => setFirstame(e.target.value)}
                                        required
                                    />
                                    <TextField
                                        id="lastname"
                                        label="Nom"
                                        type="text"
                                        variant="outlined"
                                        value={lastname}
                                        onChange={(e) => setLastname(e.target.value)}
                                        required
                                    />
                                    <TextField
                                        id="email"
                                        label="Email"
                                        type={"email"}
                                        variant="outlined"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        required
                                    />
                                </div>
                                <div className={Style.inputGroup}>
                                    <TextField
                                        id="login"
                                        label="Nom d'utilisateur"
                                        variant="outlined"
                                        value={login}
                                        onChange={(e) => setLogin(e.target.value)}
                                        required
                                    />
                                    <TextField
                                        id="password"
                                        label="Mot de passe"
                                        variant="outlined"
                                        type="password"
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        required
                                    />
                                    <TextField
                                        id="confirmPassword"
                                        label="Confirmez votre mot de passe"
                                        type="password"
                                        variant="outlined"
                                        color={'secondary'}
                                        value={confirmPassword}
                                        onChange={(e) => setConfirmPassword(e.target.value)}
                                        required
                                    />
                                    {passwordError &&
                                        <p className={Style.error}>
                                            Mot de passe incorrect !
                                        </p>
                                    }
                                </div>
                            </div>
                            <div className={Style.btnGroup}>
                                <Button className={Style.btn}
                                        variant="text"
                                        type={'submit'}
                                >
                                    S'inscrire
                                </Button>
                                <Link to="/login">
                                        Déja un compte ? Se connecter ici !
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Register;