import React, {useState} from 'react';
import Style from '../../Styles/User/Profil.module.css';
import {ErrorType} from "../../Pages/Register";
import axios from "axios";
import {useAuth} from "../Authentication/AuthProvider";
import {Alert} from "@mui/material";
import TextField from "@mui/material/TextField";
import {UserType} from "../../Pages/User/Profil";
import Button from "@mui/material/Button";
import IconButton from "@mui/material/IconButton";
import CloseIcon from "@mui/icons-material/Close";
import Snackbar from "@mui/material/Snackbar";

type ProfilFormProps = {
    user: UserType
}

export default function ProfilForm({user}: ProfilFormProps): React.JSX.Element {
    const authProvider = useAuth();
    const [email, setEmail] = useState<string>(user.email);
    const [firstname, setFirstname] = useState<string>(user.firstname);
    const [lastname, setLastname] = useState<string>(user.lastname);
    const [login, setLogin] = useState<string>(user.login);
    const [open, setOpen] = useState<boolean>(false);
    const [error, setError] = useState<ErrorType>(
        {
            isError: false,
            message: '',
            type: '',
        }
    )

    const handleOpenToast = (error: boolean) => {
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
        setError({
            isError: false,
            message: '',
            type: '',
        })
    };
    const action = (
        <React.Fragment>
            <IconButton
                size="small"
                aria-label="close"
                color="inherit"
                onClick={handleClose}
            >
                <CloseIcon fontSize="small" />
            </IconButton>
        </React.Fragment>
    );

    const handleEditUser = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            const response = await axios.put('http://localhost:8000/api/users',
                {
                    login,
                    email,
                    firstname,
                    lastname
                }, {
                headers: {
                    Authorization: `Bearer ${authProvider.getToken()}`,
                },
            });

            const data = response.data;
            if (response.status === 200) {
                authProvider.setToken(data.token);
                setEmail(data.user.email);
                setLastname(data.user.lastname);
                setFirstname(data.user.firstname);
                setLogin(data.user.login);
                handleOpenToast(false);
            }
        } catch (data: any) {
            if (data.response.data.message) {
                setError({
                    isError: true,
                    message: data.response.data.message,
                    type: data.response.data.type
                });
                handleOpenToast(true);
            }
        }
    }

    return (
        <>
            <form onSubmit={(e) => handleEditUser(e)} className={Style.form}>
                <div className={Style.formGroup}>
                    <div className={Style.inputGroup}>
                        <TextField
                            id="email"
                            label="Email"
                            type={"email"}
                            variant="outlined"
                            className={Style.input}
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            required
                            error={error.isError && error.type === 'email'}
                        />
                        <TextField
                            id="login"
                            label="Nom d'utilisateur"
                            variant="outlined"
                            value={login}
                            className={Style.input}
                            onChange={(e) => setLogin(e.target.value)}
                            required
                            error={error.isError && error.type === 'login'}
                        />
                    </div>
                    <div className={Style.inputGroup}>
                        <TextField
                            id="firstname"
                            label="Prénom"
                            variant="outlined"
                            className={Style.input}
                            value={firstname}
                            onChange={(e) => setFirstname(e.target.value)}
                            required
                        />
                        <TextField
                            id="lastname"
                            label="Nom"
                            type="text"
                            variant="outlined"
                            className={Style.input}
                            value={lastname}
                            onChange={(e) => setLastname(e.target.value)}
                            required
                        />

                    </div>
                </div>
                <div className={Style.btnGroup}>
                    <Button type={'submit'} className={Style.btn}>Modifier</Button>
                </div>
            </form>
            <Snackbar
                open={open}
                action={action}
                anchorOrigin={{
                    vertical: 'top',
                    horizontal: 'center',
                }}
            >
                {error.isError ?
                    <Alert onClose={handleClose} severity={"error"}>
                        {error.message}
                    </Alert>
                :
                    <Alert onClose={handleClose} severity="success">
                        Votre profil a bien été modifié
                    </Alert>
                }
            </Snackbar>
        </>

    );
}

