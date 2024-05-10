import React from 'react';
import { Button, StyleSheet, View } from 'react-native';
import * as Google from 'expo-auth-session/providers/google';
import { useAuthRequest } from 'expo-auth-session';
import { ANDROID_CLIENT_ID } from '../../config';




export default function GoogleSignInButton() {
    const [request, response, promptAsync] = Google.useIdTokenAuthRequest({
      clientId: ANDROID_CLIENT_ID,
    });
  
    React.useEffect(() => {
      if (response?.type === 'success') {
        const { id_token } = response.params;
        // Handle id_token, for example, send it to your backend server for authentication
      }
    }, [response]);
  
    return (
      <View style={styles.container}>
        <Button
          disabled={!request}
          title="Sign in with Google"
          onPress={() => {
            promptAsync();
          }}
        />
      </View>
    );
  }
  