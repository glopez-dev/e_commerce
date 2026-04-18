import React, {useEffect} from 'react';
import axios from "axios";
import {API_BASE_URL} from "../../config";
import Style from '../../Styles/User/Profil.module.css';
import {useAuth} from "../../Components/Authentication/AuthProvider";
import ProfilForm from "../../Components/User/ProfilForm";

export type UserType = {
    id: number,
    login: string,
    firstname: string,
    lastname: string,
    email: string,
}

export default function Profil(): React.JSX.Element {

    const authProvider = useAuth();
    const [user, setUser] = React.useState<UserType|null>(null);

    useEffect(() => {
        const fetchUser = async () => {
            try {
                const response = await axios.get(`${API_BASE_URL}/api/users`, {
                    headers: {
                        Authorization: `Bearer ${authProvider.getToken()}`,
                    },
                });
                if (response.status === 200) {
                    setUser(response.data);
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }
        fetchUser();
    }, []);


    return (
        <div className={Style.container}>
            <div className={Style.main}>
                <div className={Style.title}>
                    <h1>Votre compte</h1>
                </div>
                <div className={Style.profil}>
                    {user &&
                        <ProfilForm user={user}/>
                    }
                </div>
            </div>
        </div>
    );
}
