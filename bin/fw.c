#include <stdio.h>
#include <sys/types.h>
#include <unistd.h>
#include <stdlib.h>

/*
   our goal with this program is to interact with nftables for firewall configurations.
*/

FILE *fp; //file pointer for popen
char data[1024];
int status = 0;

int main(int argc, char** argv) {
    setuid(0);
    //thanks to: https://bugzilla.redhat.com/show_bug.cgi?id=1635238, i know that you need this command for json export.
    fp = popen("nft export vm json", "r"); //a fixed command to execute for now.

    if (fp == NULL) {
        printf("Could not exec the process...\n");
        return 0;
    }
    while (fgets(data, 1024, fp) != NULL) {
        printf("%s", data);
    }

    status = pclose(fp); //close the handle.
}