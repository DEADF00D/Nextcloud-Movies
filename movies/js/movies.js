class Movies{
    static userscoreToClass(userscore){
        var userscore_class='';
        userscore=parseFloat(userscore);
        if(userscore<=14){
            userscore_class='userscore-bad';
        }
        if(userscore>14 && userscore <= 28){
            userscore_class='userscore-badbb';
        }
        if(userscore>28 && userscore <= 42){
            userscore_class='userscore-medium';
        }
        if(userscore>42 && userscore <= 56){
            userscore_class='userscore-mediumbb';
        }
        if(userscore>56 && userscore <= 70){
            userscore_class='userscore-good';
        }
        if(userscore>70 && userscore <= 84){
            userscore_class='userscore-goodbb';
        }
        if(userscore>84 && userscore <= 100){
            userscore_class='userscore-awesome';
        }

        return userscore_class;
    }
}
