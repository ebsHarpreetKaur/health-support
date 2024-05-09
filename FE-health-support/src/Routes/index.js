import * as React from 'react';
// import * as SecureStore from 'expo-secure-store';
import { StyleSheet, Text, View, Button, ActivityIndicator } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createMaterialBottomTabNavigator } from '@react-navigation/material-bottom-tabs';
import MaterialCommunityIcons from 'react-native-vector-icons/MaterialCommunityIcons';
import MaterialIcons from 'react-native-vector-icons/MaterialIcons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useEffect, useState } from 'react';
import AuthContext from '../api/context/Context';
import FontAwesome5 from 'react-native-vector-icons/FontAwesome5';
import FontAwesome from 'react-native-vector-icons/FontAwesome';
import Login from '../Components/Login';
import Chat from '../Bottom_Tabs/Chat';
import Home from '../Bottom_Tabs/Home';
import Notification from '../Bottom_Tabs/Notification';
// import { theme_color } from '../../config';
import Account from './../Bottom_Tabs/Account';


const Stack = createNativeStackNavigator()
const Tab = createBottomTabNavigator()



function HomeScreen({ navigation }) {
    return (
        <>
            <Home />
        </>
    );
}

function AccountScreen({ navigation }) {
    return (
        <>
            <Account />
        </>
    );
}

function ChatScreen({ navigation }) {
    return (
        <>
            <Chat />
        </>
    );
}

function SignInScreen({ navigation }) {
    return (
        <>
            <Login />
        </>
    );
}

function NotificationScreen({ navigation }) {
    return (
        <>
            <Notification />
        </>
    );
}


// const user = [
//     {
//         "message": "User logged in successfully",
//         "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEwNTAzMTM3LCJleHAiOjE3MTA1MDY3MzcsIm5iZiI6MTcxMDUwMzEzNywianRpIjoiOHdLc0JRTGV1RW1TY2VuMyIsInN1YiI6IjY1ZjQxMTAzOWU0N2NhOTdiYzA5NWFhMiIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.kUu8onNc5GS3DfVF3zo4zBFTtzD2pKCNAaeygDBzFcU",
//         // "access_token": "",
//         "user": {
//             "_id": "65f411039e47ca97bc095aa2",
//             "mobile": 4675336343,
//             "otp_status": true,
//             "user_location": [
//                 {
//                     "coords": {
//                         "speed": -1,
//                         "longitude": 76.69112317715411,
//                         "latitude": 30.71134927265382,
//                         "accuracy": 16.965582688710988,
//                         "heading": -1,
//                         "altitude": 318.2151985168457,
//                         "altitudeAccuracy": 7.0764055252075195
//                     },
//                     "timestamp": 1709037095653.2131
//                 }
//             ],
//             "status": true,
//             "email": "g@gmail.co",
//             "user_pincode": 3953553,
//             "name": "hhs",
//             "payment_res": [
//                 {
//                     "amount": 100,
//                     "currency": "USD",
//                     "card_number": "4111111111111111",
//                     "card_exp_month": "12",
//                     "card_exp_year": "2025",
//                     "card_cvv": "123",
//                     "billing_address": {
//                         "line1": "123 Billing St",
//                         "line2": null,
//                         "city": "Billing City",
//                         "state": "CA",
//                         "postal_code": "12345",
//                         "country": "US"
//                     },
//                     "customer_name": "John Doe",
//                     "customer_email": "john.doe@example.com",
//                     "customer_phone": "+1234567890",
//                     "description": "Payment for order #12345",
//                     "metadata": {
//                         "order_id": "12345",
//                         "customer_id": "67890"
//                     }
//                 }
//             ],
//             "payment_status": true,
//             "updated_at": "2024-03-15T09:12:35.474000Z",
//             "created_at": "2024-03-15T09:12:35.474000Z"
//         },
//         "token_type": "Bearer",
//         "expires_in": 3600
//     }
// ]



// const user = []
//         setAuth_user(user[0])
//     };



//     auth_user_data();
// }, []);




export default function AppNavigation() {

    const [state, dispatch] = React.useReducer(
        (prevState, action) => {
            switch (action.type) {
                case 'RESTORE_TOKEN':
                    return {
                        ...prevState,
                        userToken: action.token,
                        isLoading: false,
                    };
                case 'SIGN_IN':
                    return {
                        ...prevState,
                        isSignout: false,
                        userToken: action.token,

                    };
                case 'SIGN_OUT':
                    return {
                        ...prevState,
                        isSignout: true,
                        userToken: null,

                    };
            }
        },
        {
            isLoading: true,
            isSignout: false,
            userToken: null,
        }
    );

    console.log("first", state)
    React.useEffect(() => {
        // Fetch the token from storage then navigate to our appropriate place
        const bootstrapAsync = async () => {
            // const userData = await AsyncStorage.getItem('auth_user');
            // const parsedUserData = JSON.parse(userData);
            // let userToken = parsedUserData?.access_token
            // let user_payment_status = parsedUserData?.user?.payment_status


            // After restoring token, we may need to validate it in production apps

            // This will switch to the App screen or Auth screen and this loading
            // screen will be unmounted and thrown away.
            dispatch({
                type: 'RESTORE_TOKEN', token: null
                // "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.lAS we4r5yt67u8i9o0-[\eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEwNTAzMTM3LCJleHAiOjE3MTA1MDY3MzcsIm5iZiI6MTcxMDUwMzEzNywianRpIjoiOHdLc0JRTGV1RW1TY2VuMyIsInN1YiI6IjY1ZjQxMTAzOWU0N2NhOTdiYzA5NWFhMiIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.kUu8onNc5GS3DfVF3zo4zBFTtzD2pKCNAaeygDBzFcU" 
            });
        };

        bootstrapAsync();
    }, []);

    const authContext = React.useMemo(
        () => ({
            signIn: async (data) => {
                console.log("Data", data)
                // const auth_token = data?.modifiedResponse?.access_token

                // In a production app, we need to send some data (usually username, password) to server and get a token
                // We will also need to handle errors if sign in failed
                // After getting token, we need to persist the token using `SecureStore`
                // In the example, we'll use a dummy token

                dispatch({
                    type: 'SIGN_IN', token:
                        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEwNTAzMTM3LCJleHAiOjE3MTA1MDY3MzcsIm5iZiI6MTcxMDUwMzEzNywianRpIjoiOHdLc0JRTGV1RW1TY2VuMyIsInN1YiI6IjY1ZjQxMTAzOWU0N2NhOTdiYzA5NWFhMiIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.kUu8onNc5GS3DfVF3zo4zBFTtzD2pKCNAaeygDBzFcU"

                });
            },
            signOut: () => {
                AsyncStorage.removeItem('auth_user')
                    .then(() => {
                        console.log('User logged out');
                        dispatch({ type: 'SIGN_OUT' })

                    })
                    .catch(error => {
                        console.error('Error removing auth_user:', error);
                    });

            },
            signUp: async (data) => {
                // In a production app, we need to send user data to server and get a token
                // We will also need to handle errors if sign up failed
                // After getting token, we need to persist the token using `SecureStore`
                // In the example, we'll use a dummy token

                dispatch({ type: 'SIGN_IN', token: 'dummy-auth-token' });
            },
        }),
        []
    );



    function Root() {
        return (
            <Tab.Navigator
                screenOptions={{
                    headerShown: false,
                }}
            >

                <Tab.Screen
                    name="Home"
                    component={HomeScreen}
                    options={{
                        tabBarLabel: 'Dealers',
                        tabBarActiveTintColor: "#0066b2",
                        tabBarIcon: ({ color, size }) => (
                            <MaterialCommunityIcons name="home-analytics" color={color} size={size} />
                        ),
                    }}
                />

                <Tab.Screen
                    name="Chat"
                    component={ChatScreen}
                    options={{
                        tabBarLabel: 'Chat',
                        tabBarActiveTintColor: "#0066b2",
                        tabBarIcon: ({ color, size }) => (
                            <MaterialCommunityIcons name="chat" color={color} size={size} />
                        ),
                    }}
                />
                <Tab.Screen
                    name="Notifications"
                    component={NotificationScreen}
                    options={{
                        tabBarLabel: 'Notifications',
                        tabBarActiveTintColor: "#0066b2",
                        tabBarIcon: ({ color, size }) => (
                            <MaterialCommunityIcons name="bell" color={color} size={size} />
                        ),
                    }}
                />
                <Tab.Screen
                    name="Account"
                    component={AccountScreen}
                    options={{
                        tabBarLabel: 'Account',
                        tabBarActiveTintColor: "#0066b2",
                        // tabBarInactiveBackgroundColor:"#",
                        tabBarIcon: ({ color, size }) => (
                            <MaterialCommunityIcons name="account" color={color} size={size} />
                        ),
                    }}
                />
                {/* <Tab.Screen
                    name="Login"
                    component={Login}
                /> */}

            </Tab.Navigator>
        );
    }
    // console.log("state....", state)
    function MyStack() {

        return (
            <AuthContext.Provider value={authContext}>
                <Stack.Navigator
                    screenOptions={{
                        headerShown: false,
                    }}
                >
                    {state.userToken === undefined || state.userToken === null ? (
                        <>
                            <Stack.Screen name="Login" component={SignInScreen} />
                        </>
                    ) :
                        <>
                            <Stack.Screen
                                name=" "
                                component={Root}
                            // options={{ headerShown: false }}
                            />
                            {/* <Stack.Screen name="AssignProperty" component={AssignProperty} /> */}

                        </>

                    }
                </Stack.Navigator>

            </AuthContext.Provider >

        );
    }

    return (

        <NavigationContainer>
            <MyStack />
        </NavigationContainer>
    )

}

