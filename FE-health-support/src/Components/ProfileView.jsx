import React, { useState } from 'react';
import { View, Text, Image, TextInput, StyleSheet, TouchableOpacity } from 'react-native';
import COLORS from '../theme/color';
import Button from './Button';
import { Dropdown } from './Dropdown';
import { useNavigation } from "@react-navigation/native";
// import { Picker } from '@react-native-picker/picker';


const ProfileView = (props) => {
  console.log("props", props?.route?.params)

  const [Name, setName] = useState("");
  const [Role, setRole] = useState("");
  const [selectedLanguage, setSelectedLanguage] = useState();

  const navigation = useNavigation();

  const registerUser = async () => {
    const headers = {
      'Content-Type': 'application/json',
    };

    try {
      const response = await axios.post('http://127.0.0.1:8000/api/register', {
        email: props?.route?.params?.user_email,
        password: props?.route?.params?.password,
        calling_number: props?.route?.params?.calling_number,
        whatsapp_number: props?.route?.params?.whatsapp_number,
        name: Name,
        role: Role,

      }, { headers });
      console.log('Registration successful:', response.data);
      return response.data;
    } catch (error) {
      console.error('Registration failed:', error);
      throw error;
    }
  };
  return (
    <View style={styles.container}>
      <View style={styles.avatarContainer}>
        <Image
          style={styles.avatar}
          source={require('../../assets/no-image.jpg')}
        />
        <TouchableOpacity style={styles.changeAvatarButton} onPress={() => {/* open image picker */ }}>
          <Text style={styles.changeAvatarButtonText}>Upload Photo</Text>
        </TouchableOpacity>
      </View>
      <View style={styles.form}>
        <Text style={styles.label}>Name</Text>
        <TextInput
          style={styles.input}
          placeholder="Please enter your name"
        />
        {/* <Picker
          selectedValue={selectedLanguage}
          onValueChange={(itemValue, itemIndex) =>
            setSelectedLanguage(itemValue)
          }>
          <Picker.Item label="Java" value="java" />
          <Picker.Item label="JavaScript" value="js" />
        </Picker> */}
        <Button
          title="Sign in"
          filled
          style={{
            marginTop: 28,
            marginBottom: 4,
          }}
          // onPress={() => {
          //   registerUser()
          // }}
          onPress={() => {
            navigation.navigate(" ")
          }}
        />
      </View>

    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    // flex: 1,
    marginTop: "20%",
    alignItems: 'center',
    justifyContent: 'center',
  },
  form: {
    width: '80%',
  },
  label: {
    marginTop: 20,
    marginBottom: 5,

    fontSize: 15
  },
  input: {
    borderColor: '#ccc',
    borderWidth: 1,
    borderRadius: 5,
    padding: 10,
    fontSize: 15,
  },
  button: {
    marginTop: 20,
    backgroundColor: COLORS.primary,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 20,
  },
  buttonText: {
    color: '#fff',
    fontSize: 15,
    marginLeft: "40%"
  },
  avatarContainer: {
    marginTop: 20,
    alignItems: 'center',
  },
  avatar: {
    width: 100,
    height: 100,
    borderRadius: 50,
  },
  changeAvatarButton: {
    marginTop: 10,
  },
  changeAvatarButtonText: {
    color: COLORS.primary,
    fontSize: 18,
  },
});

export default ProfileView;