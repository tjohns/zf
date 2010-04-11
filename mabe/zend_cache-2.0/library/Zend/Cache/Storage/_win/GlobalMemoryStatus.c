// http://msdn.microsoft.com/en-us/library/aa366770(VS.85).aspx

#include <windows.h>
#include <stdio.h>

void main(int argc, char *argv[])
{
  MEMORYSTATUSEX statex;

  statex.dwLength = sizeof (statex);

  GlobalMemoryStatusEx (&statex);

  /*
  printf ("ullTotalPhys:     %u\n", statex.ullTotalPhys);
  printf ("ullAvailPhys:     %u\n", statex.ullAvailPhys);
  printf ("ullTotalPageFile: %u\n", statex.ullTotalPageFile);
  printf ("ullAvailPageFile: %u\n", statex.ullAvailPageFile);
  printf ("ullTotalVirtual:  %u\n", statex.ullTotalVirtual);
  printf ("ullAvailVirtual:  %u\n", statex.ullAvailVirtual);
  */

  // display as serialized php array
  printf("a:6:{");
    printf("s:9:\"TotalPhys\";d:%u;",      statex.ullTotalPhys);
    printf("s:9:\"AvailPhys\";d:%u;",      statex.ullAvailPhys);
    printf("s:13:\"TotalPageFile\";d:%u;", statex.ullTotalPageFile);
    printf("s:13:\"AvailPageFile\";d:%u;", statex.ullAvailPageFile);
    printf("s:12:\"TotalVirtual\";d:%u;",  statex.ullTotalVirtual);
    printf("s:12:\"AvailVirtual\";d:%u;",  statex.ullAvailVirtual);
  printf("}");

}
