import { View, Text, Image, Pressable, TextInput, TouchableOpacity, StyleSheet, StatusBar } from 'react-native'
import React, { useState, useEffect } from 'react'
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import Checkbox from "expo-checkbox"
import COLORS from '../theme/color';
import Button from './Button';
// import * as WebBrowser from "expo-web-browser";
// import * as Google from "expo-auth-session/providers/google";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { ANDROID_CLIENT_ID } from '../../config';


import {
    GoogleSignin,
    GoogleSigninButton,
    statusCodes,
} from "@react-native-google-signin/google-signin";

// WebBrowser.maybeCompleteAuthSession();
const Login = ({ navigation }) => {
    const [isPasswordShown, setIsPasswordShown] = useState(false);
    const [isChecked, setIsChecked] = useState(false);
    const [error, setError] = useState();
    const [userInfo, setUserInfo] = useState();
    const [Email, setEmail] = useState("");
    const [Password, setPassword] = useState("");


    const configureGoogleSignIn = () => {
        GoogleSignin.configure({
            webClientId:
                "72307864794-vrqf4k859s84o77u52b06l2btc1ap73o.apps.googleusercontent.com",
            androidClientId:
                "72307864794-v5k7ua1dkhnb3f8s1ccnru8b5gfa70ck.apps.googleusercontent.com",
            iosClientId:
                "72307864794-1vuq2apibl4tg6on2f2nmoq5vul3ltvq.apps.googleusercontent.com",
        });
    };

    useEffect(() => {
        configureGoogleSignIn();
    });

    const signIn = async () => {
        console.log("Pressed sign in");

        try {
            await GoogleSignin.hasPlayServices();
            const userInfo = await GoogleSignin.signIn();
            setUserInfo(userInfo);
            setError();
        } catch (e) {
            setError(e);
        }
    };

    const logout = () => {
        setUserInfo(undefined);
        GoogleSignin.revokeAccess();
        GoogleSignin.signOut();
    };


    return (
        <SafeAreaView style={{ flex: 1, backgroundColor: COLORS.white }}>

            <View style={{ flex: 1, marginHorizontal: 22, marginVertical: "20%" }}>

                {/* <View style={{ marginVertical: 22 }}>

                    <Text style={{
                        fontSize: 22,
                        fontWeight: 'bold',
                        marginVertical: 12,
                        color: COLORS.black
                    }}>
                        Hi Welcome Back ! 👋
                    </Text>

                    <Text style={{
                        fontSize: 16,
                        color: COLORS.black
                    }}>Hello again you have been missed!</Text>
                </View> */}

                <View style={{ marginBottom: 12 }}>
                    <Image
                        source={require('../../assets/MediCare-logo-withoutbg.png')}
                        style={{ width: 100, height: 100, marginLeft: "35%", marginBottom: "5%" }}
                    />
                    <Text style={{
                        fontSize: 16,
                        fontWeight: 400,
                        marginVertical: 8
                    }}>Email address</Text>

                    <View style={{
                        width: "100%",
                        height: 48,
                        borderColor: COLORS.black,
                        borderWidth: 1,
                        borderRadius: 8,
                        alignItems: "center",
                        justifyContent: "center",
                        paddingLeft: 22
                    }}>
                        <TextInput
                            placeholder='Enter your email address'
                            placeholderTextColor={COLORS.black}
                            keyboardType='email-address'
                            style={{
                                width: "100%"
                            }}
                        />
                    </View>
                </View>

                <View style={{ marginBottom: 12 }}>
                    <Text style={{
                        fontSize: 16,
                        fontWeight: 400,
                        marginVertical: 8
                    }}>Password</Text>

                    <View style={{
                        width: "100%",
                        height: 48,
                        borderColor: COLORS.black,
                        borderWidth: 1,
                        borderRadius: 8,
                        alignItems: "center",
                        justifyContent: "center",
                        paddingLeft: 22
                    }}>
                        <TextInput
                            placeholder='Enter your password'
                            placeholderTextColor={COLORS.black}
                            secureTextEntry={isPasswordShown}
                            style={{
                                width: "100%"
                            }}
                        />

                        <TouchableOpacity
                            onPress={() => setIsPasswordShown(!isPasswordShown)}
                            style={{
                                position: "absolute",
                                right: 12
                            }}
                        >
                            {
                                isPasswordShown == true ? (
                                    <Ionicons name="eye-off" size={24} color={COLORS.black} />
                                ) : (
                                    <Ionicons name="eye" size={24} color={COLORS.black} />
                                )
                            }

                        </TouchableOpacity>
                    </View>
                </View>

                <View style={{
                    flexDirection: 'row',
                    marginVertical: 6
                }}>
                    <Checkbox
                        style={{ marginRight: 8 }}
                        value={isChecked}
                        onValueChange={setIsChecked}
                        color={isChecked ? COLORS.primary : undefined}
                    />

                    <Text>Remenber Me</Text>
                </View>

                <Button
                    title="Sign in"
                    filled
                    style={{
                        marginTop: 18,
                        marginBottom: 4,
                    }}
                />

                <View style={{ flexDirection: 'row', alignItems: 'center', marginVertical: 20 }}>
                    <View
                        style={{
                            flex: 1,
                            height: 1,
                            backgroundColor: COLORS.grey,
                            marginHorizontal: 10
                        }}
                    />
                    <Text style={{ fontSize: 14 }}>Or Sign in with</Text>
                    <View
                        style={{
                            flex: 1,
                            height: 1,
                            backgroundColor: COLORS.grey,
                            marginHorizontal: 10
                        }}
                    />
                </View>

                <View style={{
                    flexDirection: 'row',
                    justifyContent: 'center'
                }}>

                    <View style={styles.container}>
                        <Text>{JSON.stringify(error)}</Text>
                        {userInfo && <Text>{JSON.stringify(userInfo.user)}</Text>}
                        {userInfo ? (
                            <Button title="Logout" onPress={logout} />
                        ) : (
                            <GoogleSigninButton
                                size={GoogleSigninButton.Size.Standard}
                                color={GoogleSigninButton.Color.Dark}
                                onPress={signIn}
                            />
                        )}
                        <StatusBar style="auto" />
                    </View>


                    {/* <TouchableOpacity
                        onPress={() => console.log("Pressed")}
                        style={{
                            flex: 1,
                            alignItems: 'center',
                            justifyContent: 'center',
                            flexDirection: 'row',
                            height: 52,
                            borderWidth: 1,
                            borderColor: COLORS.grey,
                            marginRight: 4,
                            borderRadius: 10
                        }}
                    >
                        <Image
                            // source={require("../assets/facebook.png")}
                            source={require("../../assets/facebook.png")}

                            style={{
                                height: 36,
                                width: 36,
                                marginRight: 8
                            }}
                            resizeMode='contain'
                        />

                        <Text>Facebook</Text>
                    </TouchableOpacity> */}

                    {/* <TouchableOpacity
                        onPress={() => console.log("Pressed")}
                        style={{
                            flex: 1,
                            alignItems: 'center',
                            justifyContent: 'center',
                            flexDirection: 'row',
                            height: 52,
                            borderWidth: 1,
                            borderColor: COLORS.grey,
                            marginRight: 4,
                            borderRadius: 10
                        }}
                    >
                        <Image
                            source={require("../../assets/google.png")}
                            style={{
                                height: 36,
                                width: 36,
                                marginRight: 8
                            }}
                            resizeMode='contain'
                        />

                        <Text>Google</Text>
                    </TouchableOpacity> */}
                </View>

                {/* <View style={{
                    flexDirection: "row",
                    justifyContent: "center",
                    marginVertical: 22
                }}>
                    <Text style={{ fontSize: 16, color: COLORS.black }}>Don't have an account ? </Text>
                    <Pressable
                        onPress={() => navigation.navigate("Signup")}
                    >
                        <Text style={{
                            fontSize: 16,
                            color: COLORS.primary,
                            fontWeight: "bold",
                            marginLeft: 6
                        }}>Register</Text>
                    </Pressable>
                </View> */}
            </View>
        </SafeAreaView>
    )
}

export default Login



const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: "#fff",
        alignItems: "center",
        justifyContent: "center",
    },
});