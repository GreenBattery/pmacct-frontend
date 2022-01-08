#include <stdio.h>
#include <sys/types.h>
#include <unistd.h>
#include <stdlib.h>
#include <string.h>

/*
   our goal with this program is to interact with nftables for firewall configurations.
*/

FILE *fp; //file pointer for popen
char data[1024];
int status = 0;

int main(int argc, char** argv) {
    setuid(0);
    //thanks to: https://bugzilla.redhat.com/show_bug.cgi?id=1635238, i know that you need this command for json export.
    fp = popen("nft -j list ruleset", "r"); //a fixed command to execute for now.

    if (fp == NULL) {
        printf("Could not exec the process...\n");
        return 0;
    }

    int len = 0;

    /*while (fgets(data, 1024, fp) != NULL) {
        fwrite(data,strlen(data),1,stdout);
        
    }*/

    while (!feof(fp)) {
        len = fread(data, 1, 1024, fp);
        fwrite(data, 1, len, stdout);
    }

    

    status = pclose(fp); //close the handle.
}